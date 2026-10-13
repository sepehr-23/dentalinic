using System;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;
using AIBrowserAssistant.Browser;
using AIBrowserAssistant.Database;
using AIBrowserAssistant.Models;

namespace AIBrowserAssistant.Services
{
    public class AiAgentEngine
    {
        private readonly LlmClient _llmClient;
        private readonly MemoryManager _memoryManager;
        private readonly WebBrowserController _browserController;

        public event EventHandler<string> ThinkingLogGenerated;

        public AiAgentEngine(LlmClient llmClient, MemoryManager memoryManager, WebBrowserController browserController)
        {
            _llmClient = llmClient ?? throw new ArgumentNullException(nameof(llmClient));
            _memoryManager = memoryManager ?? throw new ArgumentNullException(nameof(memoryManager));
            _browserController = browserController ?? throw new ArgumentNullException(nameof(browserController));
        }

        public async Task LearnFromUserClickAsync(ActionLog clickLog)
        {
            if (clickLog == null) return;

            _memoryManager.SaveActionLog(clickLog);
            LogThinking($"🧠 ثبت کلیک جدید کاربر: روی '[{clickLog.ElementText}]' در آدرس {clickLog.Url}");

            // Analyze intent behind the click if element has meaningful text
            if (!string.IsNullOrWhiteSpace(clickLog.ElementText) && clickLog.ElementText.Length >= 2)
            {
                string text = clickLog.ElementText.ToLower();
                string intent = clickLog.ElementText;

                // Map standard keywords to Persian intent patterns
                if (text.Contains("wed") || text.Contains("wednesday"))
                {
                    intent = "مشاهده کلاس‌ها و برنامه روز چهارشنبه";
                }
                else if (text.Contains("fri") || text.Contains("friday"))
                {
                    intent = "مشاهده کلاس‌ها و برنامه روز جمعه";
                }

                var knowledge = new LearnedKnowledge
                {
                    IntentPattern = intent,
                    TargetDomain = clickLog.Domain,
                    TargetUrl = clickLog.Url,
                    ElementSelector = !string.IsNullOrEmpty(clickLog.CssSelector) ? clickLog.CssSelector : $"text:{clickLog.ElementText}",
                    TargetKeywords = clickLog.ElementText,
                    ActionType = "Click",
                    Description = $"الگوی یادگیری شده خودکار از کلیک کاربر روی دکمه یا لینک {clickLog.ElementText}",
                    CreatedAt = DateTime.Now,
                    UpdatedAt = DateTime.Now
                };

                _memoryManager.SaveKnowledge(knowledge);
                LogThinking($"✅ الگوی جدید در حافظه هوش مصنوعی ذخیره شد: {knowledge.IntentPattern} (کلیدواژه: {knowledge.TargetKeywords})");
            }
        }

        public async Task<string> ProcessUserCommandAsync(string userPrompt, string currentUrl)
        {
            LogThinking($"📥 دریافت دستور جدید کاربر: '{userPrompt}'");

            if (string.IsNullOrWhiteSpace(_llmClient.ApiKey) && _memoryManager.FindMatchingKnowledge(userPrompt, "") == null)
            {
                return "⚠️ کلید API هنوز در برنامه تنظیم نشده است. لطفا ابتدا به تب '⚙️ تنظیمات API و رفع تحریم' بروید و کلید API خود را وارد و ذخیره کنید. (همچنین می‌توانید بدون API، خودتان روی دکمه‌های سایت کلیک کنید تا برنامه آنها را یاد بگیرد).";
            }

            LogThinking("🔎 در حال بررسی حافظه دیتابیس برای الگوهای قبلاً یادگرفته‌شده...");

            Uri uri = null;
            string currentDomain = "";
            try
            {
                if (!string.IsNullOrEmpty(currentUrl))
                {
                    uri = new Uri(currentUrl);
                    currentDomain = uri.Host;
                }
            }
            catch { }

            // 1. Direct Knowledge Base Match
            var matchedKnowledge = _memoryManager.FindMatchingKnowledge(userPrompt, currentDomain);

            if (matchedKnowledge != null)
            {
                LogThinking($"💡 الگوی منطبق در حافظه یافت شد! ID: {matchedKnowledge.Id} - کلیدواژه: {matchedKnowledge.TargetKeywords}");
                LogThinking($"⚡ در حال اجرای کلیک خودکار بر اساس حافظه...");

                bool clicked = false;
                if (!string.IsNullOrEmpty(matchedKnowledge.TargetKeywords))
                {
                    clicked = await _browserController.ClickElementByTextAsync(matchedKnowledge.TargetKeywords);
                }

                if (!clicked && !string.IsNullOrEmpty(matchedKnowledge.ElementSelector))
                {
                    clicked = await _browserController.ClickElementByCssSelectorAsync(matchedKnowledge.ElementSelector);
                }

                if (clicked)
                {
                    LogThinking("✅ دکمه مورد نظر در صفحه پیدا شد و روی آن کلیک گردید.");

                    // Extract page text for summary
                    await Task.Delay(1500); // Allow DOM update
                    string pageSummaryText = await _browserController.GetVisiblePageTextAsync();

                    string summaryPrompt = $"کاربر دستور داده: '{userPrompt}'. هوش مصنوعی روی دکمه مربوطه کلیک کرده است.\nمتن صفحه فعلی:\n{pageSummaryText}\nلطفاً پاسخ دهید که عملیات انجام شد و خلاصه‌ای کوتاه از اطلاعات پیدا شده (مانند ساعت کلاس‌ها و تاریخ) به فارسی ارائه دهید.";
                    string aiResponse = await _llmClient.GenerateResponseAsync("تو یک دستیار هوشمند اتوماسیون مرورگر هستی. خلاصه اطلاعات را کوتاه و دقیق بگو.", summaryPrompt);

                    return $"بله متوجه شدم. روی گزینه مربوطه ({matchedKnowledge.TargetKeywords}) کلیک کردم.\n\n📊 خلاصه اطلاعات دریافت شده:\n{aiResponse}";
                }
            }

            // 2. LLM Reasoning fallback if direct match didn't click
            LogThinking("🤖 در حال تحلیل آنلاین با مدل هوش مصنوعی...");
            string pageText = await _browserController.GetVisiblePageTextAsync();

            string systemPrompt = @"تو یک دستیار هوشمند مرورگر وب هستی که به زبان فارسی پاسخ می‌دهی.
اگر کاربر یک احوالپرسی ساده (مثل سلام، چطوری) یا یک سوال عمومی پرسید، نیاز به کلیک روی دکمه نیست (isActionNeeded را false بگذار).
اگر کاربر خواست روی گزینه‌ای در صفحه کلیک شود (مثل روزهای هفته: چهارشنبه، جمعه، Wed، Fri، یا دکمه‌های دیگر):
- isActionNeeded را true بگذار.
- targetKeywords کلمه یا کلمات انگلیسی/فارسی روی آن دکمه را قرار بده.

خروجی را حتماً و فقط به فرمت JSON زیر ارسال کن:
{
  ""isActionNeeded"": true یا false,
  ""thinking"": ""استدلال هوش مصنوعی به فارسی"",
  ""targetKeywords"": ""کلمه روی دکمه (مثلا wed یا fri یا چهارشنبه)"",
  ""replyMessage"": ""پاسخ فارسی به کاربر""
}";

            string userPromptContext = $"آدرس فعلی مرورگر: {currentUrl}\nدستور کاربر: {userPrompt}\nمتن قابل مشاهده در صفحه فعلی:\n{pageText}";

            string llmRawResult = await _llmClient.GenerateResponseAsync(systemPrompt, userPromptContext);

            if (llmRawResult.StartsWith("⚠️") || llmRawResult.StartsWith("❌"))
            {
                return llmRawResult;
            }

            try
            {
                // Clean potential markdown wrap ```json ... ```
                string cleanJson = llmRawResult;
                if (cleanJson.Contains("```json"))
                {
                    cleanJson = cleanJson.Substring(cleanJson.IndexOf("```json") + 7);
                    if (cleanJson.Contains("```")) cleanJson = cleanJson.Substring(0, cleanJson.IndexOf("```"));
                }
                else if (cleanJson.Contains("```"))
                {
                    cleanJson = cleanJson.Substring(cleanJson.IndexOf("```") + 3);
                    if (cleanJson.Contains("```")) cleanJson = cleanJson.Substring(0, cleanJson.IndexOf("```"));
                }

                dynamic parsed = JsonConvert.DeserializeObject(cleanJson.Trim());
                bool isActionNeeded = parsed?.isActionNeeded != null ? Convert.ToBoolean(parsed.isActionNeeded) : true;
                string thinking = parsed?.thinking?.ToString();
                string targetKw = parsed?.targetKeywords?.ToString();
                string reply = parsed?.replyMessage?.ToString();

                if (!string.IsNullOrEmpty(thinking))
                {
                    LogThinking($"🧠 استدلال AI: {thinking}");
                }

                if (!isActionNeeded || string.IsNullOrWhiteSpace(targetKw))
                {
                    return !string.IsNullOrWhiteSpace(reply) ? reply : "سلام! چطور می‌توانم در وب‌گردی و اتوماسیون سایت‌ها به شما کمک کنم؟";
                }

                if (!string.IsNullOrEmpty(targetKw))
                {
                    LogThinking($"🎯 کلیک هوش مصنوعی با کلیدواژه استخراج شده: '{targetKw}'");
                    bool success = await _browserController.ClickElementByTextAsync(targetKw);

                    if (success)
                    {
                        // Save this new reasoning to memory for offline future reuse
                        var newKnowledge = new LearnedKnowledge
                        {
                            IntentPattern = userPrompt,
                            TargetDomain = currentDomain,
                            TargetUrl = currentUrl,
                            ElementSelector = $"text:{targetKw}",
                            TargetKeywords = targetKw,
                            ActionType = "Click",
                            Description = $"یادگیری استدلالی هوش مصنوعی برای دستور '{userPrompt}'",
                            CreatedAt = DateTime.Now,
                            UpdatedAt = DateTime.Now
                        };
                        _memoryManager.SaveKnowledge(newKnowledge);
                        LogThinking("💾 دانش استخراج شده جدید در دیتابیس برای دفعات بعدی ذخیره شد.");

                        return $"بله متوجه شدم. روی گزینه '{targetKw}' کلیک کردم.\n{(string.IsNullOrEmpty(reply) ? "اطلاعات مورد نظر نمایش داده شد." : reply)}";
                    }
                }
            }
            catch
            {
                // Fallback if LLM output wasn't strict JSON
            }

            return $"بله متوجه شدم، اما نتوانستم دکمه دقیقی منطبق با '{userPrompt}' در این صفحه پیدا کنم. لطفاً یک‌بار دستی روی دکمه کلیک کنید تا هوش مصنوعی حرکت شما را یاد بگیرد.";
        }

        private void LogThinking(string message)
        {
            ThinkingLogGenerated?.Invoke(this, $"[{DateTime.Now:HH:mm:ss}] {message}");
        }
    }
}
