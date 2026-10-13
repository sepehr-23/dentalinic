using System;
using System.Net;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace AIBrowserAssistant.Services
{
    public class LlmClient
    {
        private static readonly HttpClient HttpClient = new HttpClient();

        public string ApiKey { get; set; }
        public string BaseUrl { get; set; }
        public string ModelName { get; set; }
        public string Provider { get; set; } // "OpenAI", "OpenRouter", "Gemini"

        static LlmClient()
        {
            ServicePointManager.SecurityProtocol = SecurityProtocolType.Tls12 | SecurityProtocolType.Tls11 | SecurityProtocolType.Tls;
        }

        public LlmClient(string apiKey = "", string provider = "OpenRouter", string baseUrl = "", string modelName = "")
        {
            ApiKey = apiKey;
            Provider = string.IsNullOrWhiteSpace(provider) ? "OpenRouter" : provider;
            BaseUrl = string.IsNullOrWhiteSpace(baseUrl) ? ProxySanctionHelper.GetDefaultProxyUrl(Provider) : baseUrl;
            ModelName = string.IsNullOrWhiteSpace(modelName) ? GetDefaultModel(Provider) : modelName;
        }

        private string GetDefaultModel(string provider)
        {
            if (provider.Equals("OpenRouter", StringComparison.OrdinalIgnoreCase))
                return "google/gemini-2.5-flash-lite";
            if (provider.Equals("Gemini", StringComparison.OrdinalIgnoreCase))
                return "gemini-1.5-flash";
            return "gpt-3.5-turbo";
        }

        public async Task<string> GenerateResponseAsync(string systemPrompt, string userPrompt)
        {
            if (string.IsNullOrWhiteSpace(ApiKey))
            {
                return "⚠️ کلید API تنظیم نشده است. لطفا از بخش تنظیمات API کلید خود را وارد کنید.";
            }

            try
            {
                if (Provider.Equals("Gemini", StringComparison.OrdinalIgnoreCase))
                {
                    return await CallGeminiApiAsync(systemPrompt, userPrompt);
                }
                else
                {
                    return await CallOpenAiCompatibleApiAsync(systemPrompt, userPrompt);
                }
            }
            catch (WebException webEx)
            {
                return $"❌ خطا در برقراری ارتباط شبکه/پروکسی: {webEx.Message}\n💡 نکته: اگر در ایران هستید، DNS شکن یا ۴۰۳ را فعال کنید یا Base URL را تغییر دهید.";
            }
            catch (Exception ex)
            {
                return $"❌ خطای عمومی در فراخوانی هوش مصنوعی: {ex.Message}";
            }
        }

        private async Task<string> CallOpenAiCompatibleApiAsync(string systemPrompt, string userPrompt)
        {
            string endpoint = BaseUrl.TrimEnd('/') + "/chat/completions";

            var requestBody = new
            {
                model = ModelName,
                messages = new[]
                {
                    new { role = "system", content = systemPrompt },
                    new { role = "user", content = userPrompt }
                },
                temperature = 0.3
            };

            string jsonContent = JsonConvert.SerializeObject(requestBody);
            var httpRequest = new HttpRequestMessage(HttpMethod.Post, endpoint);
            httpRequest.Content = new StringContent(jsonContent, Encoding.UTF8, "application/json");
            httpRequest.Headers.Add("Authorization", $"Bearer {ApiKey.Trim()}");

            if (Provider.Equals("OpenRouter", StringComparison.OrdinalIgnoreCase))
            {
                httpRequest.Headers.Add("HTTP-Referer", "https://github.com/AIBrowserAssistant");
                httpRequest.Headers.Add("X-Title", "AI Browser Assistant");
            }

            var response = await HttpClient.SendAsync(httpRequest);
            string responseJson = await response.Content.ReadAsStringAsync();

            if (!response.IsSuccessStatusCode)
            {
                return $"❌ خطا از سرور AI (کد status: {response.StatusCode}): {responseJson}";
            }

            JObject obj = JObject.Parse(responseJson);
            string result = obj["choices"]?[0]?["message"]?["content"]?.ToString();
            return result ?? "پاسخی دریافت نشد.";
        }

        private async Task<string> CallGeminiApiAsync(string systemPrompt, string userPrompt)
        {
            string url = $"{BaseUrl.TrimEnd('/')}/models/{ModelName}:generateContent?key={ApiKey.Trim()}";

            var requestBody = new
            {
                contents = new[]
                {
                    new
                    {
                        parts = new[]
                        {
                            new { text = $"{systemPrompt}\n\nورودی کاربر:\n{userPrompt}" }
                        }
                    }
                }
            };

            string jsonContent = JsonConvert.SerializeObject(requestBody);
            var httpRequest = new HttpRequestMessage(HttpMethod.Post, url);
            httpRequest.Content = new StringContent(jsonContent, Encoding.UTF8, "application/json");

            var response = await HttpClient.SendAsync(httpRequest);
            string responseJson = await response.Content.ReadAsStringAsync();

            if (!response.IsSuccessStatusCode)
            {
                return $"❌ خطا از Google Gemini API (کد status: {response.StatusCode}): {responseJson}";
            }

            JObject obj = JObject.Parse(responseJson);
            string result = obj["candidates"]?[0]?["content"]?["parts"]?[0]?["text"]?.ToString();
            return result ?? "پاسخی دریافت نشد.";
        }
    }
}
