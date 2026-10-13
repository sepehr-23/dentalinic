using System;
using System.Drawing;
using System.Threading.Tasks;
using System.Windows.Forms;
using Microsoft.Web.WebView2.WinForms;
using AIBrowserAssistant.Browser;
using AIBrowserAssistant.Database;
using AIBrowserAssistant.Models;
using AIBrowserAssistant.Services;

namespace AIBrowserAssistant
{
    public partial class Form1 : Form
    {
        private MemoryManager _memoryManager;
        private LlmClient _llmClient;
        private WebBrowserController _browserController;
        private AiAgentEngine _aiEngine;

        // Visual Controls
        private SplitContainer splitContainerMain;
        private TabControl tabControlLeft;
        private TabPage tabChat;
        private TabPage tabLogs;
        private TabPage tabSettings;

        // Chat tab controls
        private RichTextBox rtbChatHistory;
        private TextBox txtCommandInput;
        private Button btnSend;
        private CheckBox chkAutoTranslate;
        private CheckBox chkTrackingActive;
        private Button btnManageMemory;

        // Logs tab controls
        private RichTextBox rtbThinkingLogs;
        private Button btnClearLogs;

        // Settings tab controls
        private TextBox txtApiKey;
        private TextBox txtBaseUrl;
        private ComboBox cbProvider;
        private TextBox txtModelName;
        private Button btnSaveSettings;
        private RichTextBox rtbSanctionGuide;

        // Right Browser controls
        private Panel panelBrowserHeader;
        private TextBox txtUrlBar;
        private Button btnGo;
        private Button btnBack;
        private Button btnForward;
        private Button btnRefresh;
        private WebView2 webView;

        public Form1()
        {
            InitializeComponent();
            InitializeCustomComponents();
            this.Load += Form1_Load;
        }

        private void InitializeCustomComponents()
        {
            this.Text = "🤖 دستیار هوشمند مرورگر و خودکارسازی وب (C# .NET 4.8 + AI)";
            this.Size = new Size(1366, 768);
            this.RightToLeft = RightToLeft.Yes;
            this.RightToLeftLayout = true;
            this.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular, GraphicsUnit.Point);

            // Split Container
            splitContainerMain = new SplitContainer
            {
                Dock = DockStyle.Fill,
                Orientation = Orientation.Vertical,
                SplitterDistance = 460
            };
            this.Controls.Add(splitContainerMain);

            // Left Tab Control
            tabControlLeft = new TabControl { Dock = DockStyle.Fill };
            tabChat = new TabPage { Text = "💬 گفتگو و دستورات" };
            tabLogs = new TabPage { Text = "🧠 تفکر و لاگ هوش مصنوعی" };
            tabSettings = new TabPage { Text = "⚙️ تنظیمات API و رفع تحریم" };

            tabControlLeft.TabPages.Add(tabChat);
            tabControlLeft.TabPages.Add(tabLogs);
            tabControlLeft.TabPages.Add(tabSettings);
            splitContainerMain.Panel1.Controls.Add(tabControlLeft);

            SetupChatTab();
            SetupLogsTab();
            SetupSettingsTab();
            SetupBrowserPanel();
        }

        private void SetupChatTab()
        {
            var panelBottom = new Panel { Dock = DockStyle.Bottom, Height = 100, Padding = new Padding(8) };

            txtCommandInput = new TextBox
            {
                Multiline = true,
                Height = 50,
                Dock = DockStyle.Top,
                Font = new Font("Segoe UI", 10F)
            };
            txtCommandInput.KeyDown += (s, e) =>
            {
                if (e.KeyCode == Keys.Enter && !e.Shift)
                {
                    e.SuppressKeyPress = true;
                    BtnSend_Click(s, e);
                }
            };

            btnSend = new Button
            {
                Text = "🚀 ارسال دستور به AI",
                Dock = DockStyle.Left,
                Width = 150,
                Height = 35,
                BackColor = Color.FromArgb(52, 152, 219),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9.5F, FontStyle.Bold)
            };
            btnSend.Click += BtnSend_Click;

            btnManageMemory = new Button
            {
                Text = "📚 مدیریت دانش ذخیره‌شده",
                Dock = DockStyle.Right,
                Width = 180,
                Height = 35,
                BackColor = Color.FromArgb(155, 89, 182),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
            btnManageMemory.Click += (s, e) =>
            {
                using (var kbForm = new KnowledgeManagerForm(_memoryManager))
                {
                    kbForm.ShowDialog(this);
                }
            };

            var panelOptions = new Panel { Dock = DockStyle.Top, Height = 35 };

            chkAutoTranslate = new CheckBox
            {
                Text = "🌐 ترجمه خودکار صفحه به فارسی",
                AutoSize = true,
                Location = new Point(220, 5)
            };
            chkAutoTranslate.CheckedChanged += (s, e) =>
            {
                _browserController?.SetAutoTranslateActive(chkAutoTranslate.Checked);
            };

            chkTrackingActive = new CheckBox
            {
                Text = "👁️ ضبط و یادگیری کلیک‌های کاربر",
                Checked = true,
                AutoSize = true,
                Location = new Point(10, 5)
            };
            chkTrackingActive.CheckedChanged += (s, e) =>
            {
                _browserController?.SetTrackingActive(chkTrackingActive.Checked);
            };

            panelOptions.Controls.Add(chkAutoTranslate);
            panelOptions.Controls.Add(chkTrackingActive);

            var panelSendRow = new Panel { Dock = DockStyle.Bottom, Height = 40 };
            panelSendRow.Controls.Add(btnSend);
            panelSendRow.Controls.Add(btnManageMemory);

            panelBottom.Controls.Add(panelSendRow);
            panelBottom.Controls.Add(txtCommandInput);

            rtbChatHistory = new RichTextBox
            {
                Dock = DockStyle.Fill,
                ReadOnly = true,
                BackColor = Color.FromArgb(248, 249, 250),
                Font = new Font("Segoe UI", 10F)
            };

            tabChat.Controls.Add(rtbChatHistory);
            tabChat.Controls.Add(panelOptions);
            tabChat.Controls.Add(panelBottom);
        }

        private void SetupLogsTab()
        {
            rtbThinkingLogs = new RichTextBox
            {
                Dock = DockStyle.Fill,
                ReadOnly = true,
                BackColor = Color.FromArgb(30, 30, 30),
                ForeColor = Color.FromArgb(220, 220, 220),
                Font = new Font("Consolas", 9.5F)
            };

            btnClearLogs = new Button
            {
                Text = "پاکسازی لاگ‌ها",
                Dock = DockStyle.Bottom,
                Height = 35
            };
            btnClearLogs.Click += (s, e) => rtbThinkingLogs.Clear();

            tabLogs.Controls.Add(rtbThinkingLogs);
            tabLogs.Controls.Add(btnClearLogs);
        }

        private void SetupSettingsTab()
        {
            var panelFields = new Panel { Dock = DockStyle.Top, Height = 220, Padding = new Padding(10) };

            int lblX = 320, inputX = 10, width = 300;

            var lblProvider = new Label { Text = "سرویس‌دهنده AI:", Location = new Point(lblX, 15), AutoSize = true };
            cbProvider = new ComboBox { Location = new Point(inputX, 12), Width = width, DropDownStyle = ComboBoxStyle.DropDownList };
            cbProvider.Items.AddRange(new object[] { "OpenRouter", "Gemini", "OpenAI" });
            cbProvider.SelectedIndex = 0;
            cbProvider.SelectedIndexChanged += CbProvider_SelectedIndexChanged;

            var lblApiKey = new Label { Text = "کلید API (API Key):", Location = new Point(lblX, 55), AutoSize = true };
            txtApiKey = new TextBox { Location = new Point(inputX, 52), Width = width, PasswordChar = '*' };

            var lblBaseUrl = new Label { Text = "آدرس Base URL (پروکسی):", Location = new Point(lblX, 95), AutoSize = true };
            txtBaseUrl = new TextBox { Location = new Point(inputX, 92), Width = width };

            var lblModel = new Label { Text = "نام مدل (Model Name):", Location = new Point(lblX, 135), AutoSize = true };
            txtModelName = new TextBox { Location = new Point(inputX, 132), Width = width };

            btnSaveSettings = new Button
            {
                Text = "💾 ذخیره تنظیمات API",
                Location = new Point(inputX, 170),
                Width = width,
                Height = 35,
                BackColor = Color.FromArgb(46, 204, 113),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
            btnSaveSettings.Click += BtnSaveSettings_Click;

            panelFields.Controls.Add(lblProvider); panelFields.Controls.Add(cbProvider);
            panelFields.Controls.Add(lblApiKey); panelFields.Controls.Add(txtApiKey);
            panelFields.Controls.Add(lblBaseUrl); panelFields.Controls.Add(txtBaseUrl);
            panelFields.Controls.Add(lblModel); panelFields.Controls.Add(txtModelName);
            panelFields.Controls.Add(btnSaveSettings);

            rtbSanctionGuide = new RichTextBox
            {
                Dock = DockStyle.Fill,
                ReadOnly = true,
                BackColor = Color.FromArgb(253, 246, 227),
                Font = new Font("Segoe UI", 9.5F),
                Text = ProxySanctionHelper.GetSanctionBypassGuidePersian()
            };

            tabSettings.Controls.Add(rtbSanctionGuide);
            tabSettings.Controls.Add(panelFields);
        }

        private void SetupBrowserPanel()
        {
            panelBrowserHeader = new Panel { Dock = DockStyle.Top, Height = 40, Padding = new Padding(5) };

            btnBack = new Button { Text = "◀", Width = 35, Height = 30, Location = new Point(5, 5) };
            btnBack.Click += (s, e) => { if (webView?.CoreWebView2 != null && webView.CoreWebView2.CanGoBack) webView.CoreWebView2.GoBack(); };

            btnForward = new Button { Text = "▶", Width = 35, Height = 30, Location = new Point(45, 5) };
            btnForward.Click += (s, e) => { if (webView?.CoreWebView2 != null && webView.CoreWebView2.CanGoForward) webView.CoreWebView2.GoForward(); };

            btnRefresh = new Button { Text = "🔄", Width = 35, Height = 30, Location = new Point(85, 5) };
            btnRefresh.Click += (s, e) => webView?.CoreWebView2?.Reload();

            txtUrlBar = new TextBox { Location = new Point(130, 7), Width = 600, Font = new Font("Segoe UI", 10F) };
            txtUrlBar.KeyDown += (s, e) => { if (e.KeyCode == Keys.Enter) btnGo.PerformClick(); };

            btnGo = new Button { Text = "برو به سایت", Location = new Point(740, 5), Width = 100, Height = 30 };
            btnGo.Click += (s, e) => _browserController?.Navigate(txtUrlBar.Text);

            panelBrowserHeader.Controls.Add(btnBack);
            panelBrowserHeader.Controls.Add(btnForward);
            panelBrowserHeader.Controls.Add(btnRefresh);
            panelBrowserHeader.Controls.Add(txtUrlBar);
            panelBrowserHeader.Controls.Add(btnGo);

            webView = new WebView2 { Dock = DockStyle.Fill };

            splitContainerMain.Panel2.Controls.Add(webView);
            splitContainerMain.Panel2.Controls.Add(panelBrowserHeader);
        }

        private async void Form1_Load(object sender, EventArgs e)
        {
            _memoryManager = new MemoryManager();
            _llmClient = new LlmClient("", cbProvider.SelectedItem.ToString(), txtBaseUrl.Text, txtModelName.Text);

            _browserController = new WebBrowserController(webView);
            await _browserController.InitializeAsync();

            _browserController.NavigationFinished += (s, url) =>
            {
                if (InvokeRequired)
                {
                    Invoke(new Action(() => txtUrlBar.Text = url));
                }
                else
                {
                    txtUrlBar.Text = url;
                }
            };

            _browserController.UserInteractionRecorded += async (s, log) =>
            {
                if (_aiEngine != null)
                {
                    await _aiEngine.LearnFromUserClickAsync(log);
                }
            };

            _aiEngine = new AiAgentEngine(_llmClient, _memoryManager, _browserController);
            _aiEngine.ThinkingLogGenerated += (s, logMsg) =>
            {
                AppendThinkingLog(logMsg);
            };

            // Initial Navigation
            txtUrlBar.Text = "https://www.google.com";
            _browserController.Navigate(txtUrlBar.Text);

            AppendChatMessage("🤖 سیستم:", "سلام! من دستیار هوشمند مرورگر شما هستم. می‌توانید دستوراتی مثل 'برو به سایت دانشگاه و کلاس‌های چهارشنبه رو ببین' به من بدهید یا دکمه‌های سایت را کلیک کنید تا رفتار شما را یاد بگیرم.");
        }

        private async void BtnSend_Click(object sender, EventArgs e)
        {
            string command = txtCommandInput.Text.Trim();
            if (string.IsNullOrEmpty(command)) return;

            AppendChatMessage("👤 شما:", command);
            txtCommandInput.Clear();

            btnSend.Enabled = false;
            try
            {
                string response = await _aiEngine.ProcessUserCommandAsync(command, webView.Source?.ToString());
                AppendChatMessage("🤖 دستیار AI:", response);
            }
            finally
            {
                btnSend.Enabled = true;
            }
        }

        private void CbProvider_SelectedIndexChanged(object sender, EventArgs e)
        {
            string provider = cbProvider.SelectedItem.ToString();
            txtBaseUrl.Text = ProxySanctionHelper.GetDefaultProxyUrl(provider);
            if (provider == "OpenRouter") txtModelName.Text = "google/gemini-2.5-flash-lite";
            else if (provider == "Gemini") txtModelName.Text = "gemini-1.5-flash";
            else txtModelName.Text = "gpt-3.5-turbo";
        }

        private void BtnSaveSettings_Click(object sender, EventArgs e)
        {
            _llmClient.ApiKey = txtApiKey.Text.Trim();
            _llmClient.Provider = cbProvider.SelectedItem.ToString();
            _llmClient.BaseUrl = txtBaseUrl.Text.Trim();
            _llmClient.ModelName = txtModelName.Text.Trim();

            MessageBox.Show("تنظیمات API و پروکسی با موفقیت ذخیره گردید.", "موفقیت", MessageBoxButtons.OK, MessageBoxIcon.Information);
        }

        private void AppendChatMessage(string senderName, string text)
        {
            if (InvokeRequired)
            {
                Invoke(new Action(() => AppendChatMessage(senderName, text)));
                return;
            }

            rtbChatHistory.SelectionStart = rtbChatHistory.TextLength;
            rtbChatHistory.SelectionLength = 0;
            rtbChatHistory.SelectionFont = new Font("Segoe UI", 10F, FontStyle.Bold);
            rtbChatHistory.SelectionColor = senderName.Contains("شما") ? Color.Blue : Color.DarkGreen;
            rtbChatHistory.AppendText($"{senderName}\n");

            rtbChatHistory.SelectionFont = new Font("Segoe UI", 9.5F, FontStyle.Regular);
            rtbChatHistory.SelectionColor = Color.Black;
            rtbChatHistory.AppendText($"{text}\n\n");
            rtbChatHistory.ScrollToCaret();
        }

        private void AppendThinkingLog(string text)
        {
            if (InvokeRequired)
            {
                Invoke(new Action(() => AppendThinkingLog(text)));
                return;
            }

            rtbThinkingLogs.AppendText($"{text}\n");
            rtbThinkingLogs.ScrollToCaret();
        }
    }
}
