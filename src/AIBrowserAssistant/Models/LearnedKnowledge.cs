using System;

namespace AIBrowserAssistant.Models
{
    public class LearnedKnowledge
    {
        public int Id { get; set; }
        public string IntentPattern { get; set; }  // e.g. "مشاهده کلاس های چهارشنبه", "دیدن برنامه جمعه"
        public string TargetDomain { get; set; }   // e.g. "example-university.ac.ir"
        public string TargetUrl { get; set; }      // e.g. "https://example-university.ac.ir/schedule"
        public string ElementSelector { get; set; }// e.g. "#wed-classes", "button:contains('wed')"
        public string TargetKeywords { get; set; } // e.g. "wed, Wednesday, چهارشنبه"
        public string ActionType { get; set; }     // e.g. "Click", "ExtractText", "NavigateAndClick"
        public string Description { get; set; }    // e.g. "برای کلیک روی روز چهارشنبه در صفحه کلاس‌ها"
        public DateTime CreatedAt { get; set; }
        public DateTime UpdatedAt { get; set; }
    }
}
