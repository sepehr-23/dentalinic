using System;
using System.Text;

namespace AIBrowserAssistant.Services
{
    public static class ProxySanctionHelper
    {
        public static string GetSanctionBypassGuidePersian()
        {
            var sb = new StringBuilder();
            sb.AppendLine("📌 **راهنمای جامع رفع تحریم و تنظیم کلید API برای کاربران ایرانی:**");
            sb.AppendLine();
            sb.AppendLine("1. **مشکل تحریم‌های گوگل و OpenAI:**");
            sb.AppendLine("   ارائه‌دهندگان هوش مصنوعی مثل OpenAI و Google Gemini آی‌پی‌های ایران را مسدود کرده‌اند.");
            sb.AppendLine("   برای حل این مشکل بدون نیاز به VPN، می‌توانید از پروکسی‌ها و DNSهای ضد تحریم ایرانی استفاده کنید:");
            sb.AppendLine("   • **شکن (Shecan):** تنظیم Primary DNS: `178.22.122.100` و Secondary DNS: `185.51.200.2`");
            sb.AppendLine("   • **۴۰۳ آنلاین (403.online):** تنظیم Primary DNS: `10.202.10.202` و Secondary DNS: `10.202.10.102`");
            sb.AppendLine();
            sb.AppendLine("2. **استفاده از آدرس پایه سفارشی (Custom Base URL / Proxy Endpoint):**");
            sb.AppendLine("   در تنظیمات این برنامه، می‌توانید آدرس API Base URL را به پروکسی‌های مجاز تغییر دهید:");
            sb.AppendLine("   • **OpenRouter (پیش‌نهادی بدون تحریم):** `https://openrouter.ai/api/v1` (پشتیبانی از مدل‌های رایگان و بدون تحریم)");
            sb.AppendLine("   • **Gemini Reverse Proxy:** `https://generativelanguage.googleapis.com/v1beta` (با DNS ۴۰۳ یا شکن)");
            sb.AppendLine();
            sb.AppendLine("3. **چگونه کلید API رایگان دریافت کنیم؟**");
            sb.AppendLine("   • به سایت `openrouter.ai` یا `aistudio.google.com` مراجعه کنید.");
            sb.AppendLine("   • یک حساب کاربری بسازید و در بخش API Keys یک کلید جدید ایجاد و در برنامه وارد کنید.");
            sb.AppendLine("   • برنامه به صورت خودکار ارتباط را تست و ذخیره خواهد کرد.");
            return sb.ToString();
        }

        public static string GetDefaultProxyUrl(string provider)
        {
            if (provider.Equals("OpenRouter", StringComparison.OrdinalIgnoreCase))
            {
                return "https://openrouter.ai/api/v1";
            }
            if (provider.Equals("Gemini", StringComparison.OrdinalIgnoreCase))
            {
                return "https://generativelanguage.googleapis.com/v1beta";
            }
            return "https://api.openai.com/v1";
        }
    }
}
