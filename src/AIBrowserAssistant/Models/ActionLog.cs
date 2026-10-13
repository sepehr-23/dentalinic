namespace AIBrowserAssistant.Models
{
    public class ActionLog
    {
        public int Id { get; set; }
        public string Domain { get; set; }
        public string Url { get; set; }
        public string ActionType { get; set; } // Click, Input, Navigate, Scroll
        public string ElementTagName { get; set; }
        public string ElementId { get; set; }
        public string ElementText { get; set; }
        public string CssSelector { get; set; }
        public string XPath { get; set; }
        public string ExtraData { get; set; }
        public string RecordedAt { get; set; }
    }
}
