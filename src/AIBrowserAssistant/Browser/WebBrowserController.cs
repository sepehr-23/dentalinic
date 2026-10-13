using System;
using System.IO;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using Microsoft.Web.WebView2.Core;
using Microsoft.Web.WebView2.WinForms;
using Newtonsoft.Json;
using AIBrowserAssistant.Models;

namespace AIBrowserAssistant.Browser
{
    public class WebBrowserController
    {
        private readonly WebView2 _webView;
        private bool _isTrackingActive = true;
        private bool _isAutoTranslateActive = false;

        public event EventHandler<ActionLog> UserInteractionRecorded;
        public event EventHandler<string> NavigationFinished;

        public WebBrowserController(WebView2 webView)
        {
            _webView = webView ?? throw new ArgumentNullException(nameof(webView));
        }

        public async Task InitializeAsync()
        {
            await _webView.EnsureCoreWebView2Async(null);

            _webView.CoreWebView2.NavigationCompleted += OnNavigationCompleted;
            _webView.CoreWebView2.WebMessageReceived += OnWebMessageReceived;

            await InjectTrackingScriptAsync();
        }

        public void Navigate(string url)
        {
            if (string.IsNullOrWhiteSpace(url)) return;
            if (!url.StartsWith("http://") && !url.StartsWith("https://"))
            {
                url = "https://" + url;
            }
            _webView.Source = new Uri(url);
        }

        public void SetTrackingActive(bool active)
        {
            _isTrackingActive = active;
        }

        public void SetAutoTranslateActive(bool active)
        {
            _isAutoTranslateActive = active;
            if (_isAutoTranslateActive)
            {
                TranslatePageToPersianAsync();
            }
        }

        private async void OnNavigationCompleted(object sender, CoreWebView2NavigationCompletedEventArgs e)
        {
            if (e.IsSuccess)
            {
                await InjectTrackingScriptAsync();
                NavigationFinished?.Invoke(this, _webView.Source.ToString());

                if (_isAutoTranslateActive)
                {
                    await TranslatePageToPersianAsync();
                }
            }
        }

        private async Task InjectTrackingScriptAsync()
        {
            string js = @"
            (function() {
                if (window.__aiTrackerInjected) return;
                window.__aiTrackerInjected = true;

                document.addEventListener('click', function(e) {
                    var el = e.target;
                    if (!el) return;

                    var tagName = el.tagName ? el.tagName.toLowerCase() : '';
                    var elId = el.id || '';
                    var text = (el.innerText || el.textContent || el.value || '').trim().substring(0, 150);

                    // Simple CSS selector generator
                    var cssSelector = tagName;
                    if (elId) {
                        cssSelector += '#' + elId;
                    } else if (el.className && typeof el.className === 'string') {
                        var classes = el.className.split(' ').filter(c => c.length > 0).join('.');
                        if (classes) cssSelector += '.' + classes;
                    }

                    // Simple XPath generator
                    function getXPath(element) {
                        if (element.id !== '') return 'id(""' + element.id + '"")';
                        if (element === document.body) return element.tagName.toLowerCase();
                        var ix = 0;
                        var siblings = element.parentNode ? element.parentNode.childNodes : [];
                        for (var i = 0; i < siblings.length; i++) {
                            var sibling = siblings[i];
                            if (sibling === element) return getXPath(element.parentNode) + '/' + element.tagName.toLowerCase() + '[' + (ix + 1) + ']';
                            if (sibling.nodeType === 1 && sibling.tagName === element.tagName) ix++;
                        }
                    }

                    var xpath = getXPath(el) || '';

                    var payload = {
                        action: 'click',
                        url: window.location.href,
                        domain: window.location.hostname,
                        tagName: tagName,
                        elementId: elId,
                        text: text,
                        cssSelector: cssSelector,
                        xpath: xpath
                    };

                    window.chrome.webview.postMessage(payload);
                }, true);
            })();
            ";

            try
            {
                await _webView.CoreWebView2.ExecuteScriptAsync(js);
            }
            catch { }
        }

        private void OnWebMessageReceived(object sender, CoreWebView2WebMessageReceivedEventArgs e)
        {
            if (!_isTrackingActive) return;

            try
            {
                string rawJson = e.WebMessageAsJson;
                dynamic data = JsonConvert.DeserializeObject(rawJson);

                if (data != null && data.action == "click")
                {
                    var log = new ActionLog
                    {
                        Domain = data.domain,
                        Url = data.url,
                        ActionType = "Click",
                        ElementTagName = data.tagName,
                        ElementId = data.elementId,
                        ElementText = data.text,
                        CssSelector = data.cssSelector,
                        XPath = data.xpath,
                        RecordedAt = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss")
                    };

                    UserInteractionRecorded?.Invoke(this, log);
                }
            }
            catch { }
        }

        public async Task<bool> ClickElementByTextAsync(string textKeywords)
        {
            if (string.IsNullOrWhiteSpace(textKeywords)) return false;

            string[] keywords = textKeywords.Split(new[] { ',', ';', '|' }, StringSplitOptions.RemoveEmptyEntries);
            string jsonKeywords = JsonConvert.SerializeObject(keywords);

            string js = $@"
            (function() {{
                var keywords = {jsonKeywords};
                var elements = Array.from(document.querySelectorAll('a, button, input[type=""button""], input[type=""submit""], div, span, li, tr, td'));

                for (var i = 0; i < elements.length; i++) {{
                    var el = elements[i];
                    var elText = (el.innerText || el.textContent || el.value || '').toLowerCase();

                    for (var k = 0; k < keywords.length; k++) {{
                        var kw = keywords[k].trim().toLowerCase();
                        if (kw && elText.includes(kw)) {{
                            el.scrollIntoView({{ behavior: 'smooth', block: 'center' }});
                            el.style.border = '3px solid #e74c3c';
                            el.style.backgroundColor = '#fbeee6';
                            setTimeout(function() {{
                                el.click();
                            }}, 300);
                            return true;
                        }}
                    }}
                }}
                return false;
            }})();
            ";

            string resultStr = await _webView.CoreWebView2.ExecuteScriptAsync(js);
            return resultStr == "true";
        }

        public async Task<bool> ClickElementByCssSelectorAsync(string cssSelector)
        {
            if (string.IsNullOrWhiteSpace(cssSelector)) return false;

            string js = $@"
            (function() {{
                var el = document.querySelector('{cssSelector.Replace("'", "\\'")}');
                if (el) {{
                    el.scrollIntoView({{ behavior: 'smooth', block: 'center' }});
                    el.style.border = '3px solid #2ecc71';
                    el.click();
                    return true;
                }}
                return false;
            }})();
            ";

            string resultStr = await _webView.CoreWebView2.ExecuteScriptAsync(js);
            return resultStr == "true";
        }

        public async Task<string> GetVisiblePageTextAsync()
        {
            string js = @"
            (function() {
                return (document.body ? document.body.innerText : '').substring(0, 4000);
            })();
            ";

            string json = await _webView.CoreWebView2.ExecuteScriptAsync(js);
            return JsonConvert.DeserializeObject<string>(json);
        }

        public async Task TranslatePageToPersianAsync()
        {
            string js = @"
            (function() {
                if (window.__faTranslated) return;
                window.__faTranslated = true;

                var dictionary = {
                    'wednesday': 'چهارشنبه',
                    'wed': 'چهارشنبه',
                    'friday': 'جمعه',
                    'fri': 'جمعه',
                    'saturday': 'شنبه',
                    'sat': 'شنبه',
                    'sunday': 'یکشنبه',
                    'sun': 'یکشنبه',
                    'monday': 'دوشنبه',
                    'mon': 'دوشنبه',
                    'tuesday': 'سه‌شنبه',
                    'tue': 'سه‌شنبه',
                    'thursday': 'پنج‌شنبه',
                    'thu': 'پنج‌شنبه',
                    'class': 'کلاس',
                    'classes': 'کلاس‌ها',
                    'schedule': 'برنامه زمانی',
                    'time': 'ساعت',
                    'date': 'تاریخ',
                    'course': 'دوره آموزشی'
                };

                function walkTextNodes(node) {
                    if (node.nodeType === 3) { // Text node
                        var text = node.nodeValue;
                        for (var key in dictionary) {
                            var regex = new RegExp('\\b' + key + '\\b', 'gi');
                            text = text.replace(regex, dictionary[key]);
                        }
                        node.nodeValue = text;
                    } else {
                        for (var i = 0; i < node.childNodes.length; i++) {
                            walkTextNodes(node.childNodes[i]);
                        }
                    }
                }

                if (document.body) {
                    walkTextNodes(document.body);
                }
            })();
            ";

            try
            {
                await _webView.CoreWebView2.ExecuteScriptAsync(js);
            }
            catch { }
        }
    }
}
