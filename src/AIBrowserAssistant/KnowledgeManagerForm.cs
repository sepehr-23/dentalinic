using System;
using System.ComponentModel;
using System.Drawing;
using System.Windows.Forms;
using AIBrowserAssistant.Database;
using AIBrowserAssistant.Models;

namespace AIBrowserAssistant
{
    public class KnowledgeManagerForm : Form
    {
        private readonly MemoryManager _memoryManager;
        private DataGridView dgvKnowledge;
        private Button btnAdd;
        private Button btnEdit;
        private Button btnDelete;
        private Button btnRefresh;
        private Panel panelTop;
        private TextBox txtSearch;
        private Label lblSearch;

        public KnowledgeManagerForm(MemoryManager memoryManager)
        {
            _memoryManager = memoryManager ?? throw new ArgumentNullException(nameof(memoryManager));
            InitializeComponent();
            LoadData();
        }

        private void InitializeComponent()
        {
            this.Text = "مدیریت پایگاه دانش و یادگیری‌های هوش مصنوعی (Knowledge Base Manager)";
            this.Size = new Size(900, 550);
            this.RightToLeft = RightToLeft.Yes;
            this.RightToLeftLayout = true;
            this.StartPosition = FormStartPosition.CenterParent;
            this.Font = new Font("Segoe UI", 9.5F, FontStyle.Regular, GraphicsUnit.Point);

            panelTop = new Panel
            {
                Dock = DockStyle.Top,
                Height = 50,
                Padding = new Padding(10)
            };

            lblSearch = new Label
            {
                Text = "جستجو:",
                AutoSize = true,
                Location = new Point(810, 15)
            };

            txtSearch = new TextBox
            {
                Location = new Point(600, 12),
                Width = 200
            };
            txtSearch.TextChanged += TxtSearch_TextChanged;

            btnRefresh = new Button
            {
                Text = "🔄 به‌روزرسانی لیست",
                Location = new Point(470, 10),
                Width = 120,
                Height = 30
            };
            btnRefresh.Click += (s, e) => LoadData();

            btnAdd = new Button
            {
                Text = "➕ افزودن قاعده جدید",
                Location = new Point(330, 10),
                Width = 130,
                Height = 30,
                BackColor = Color.FromArgb(46, 204, 113),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
            btnAdd.Click += BtnAdd_Click;

            btnEdit = new Button
            {
                Text = "✏️ ویرایش قاعده",
                Location = new Point(200, 10),
                Width = 120,
                Height = 30
            };
            btnEdit.Click += BtnEdit_Click;

            btnDelete = new Button
            {
                Text = "🗑️ حذف قاعده",
                Location = new Point(80, 10),
                Width = 110,
                Height = 30,
                BackColor = Color.FromArgb(231, 76, 60),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat
            };
            btnDelete.Click += BtnDelete_Click;

            panelTop.Controls.Add(lblSearch);
            panelTop.Controls.Add(txtSearch);
            panelTop.Controls.Add(btnRefresh);
            panelTop.Controls.Add(btnAdd);
            panelTop.Controls.Add(btnEdit);
            panelTop.Controls.Add(btnDelete);

            dgvKnowledge = new DataGridView
            {
                Dock = DockStyle.Fill,
                AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill,
                SelectionMode = DataGridViewSelectionMode.FullRowSelect,
                MultiSelect = false,
                ReadOnly = true,
                AllowUserToAddRows = false,
                BackgroundColor = Color.White
            };

            this.Controls.Add(dgvKnowledge);
            this.Controls.Add(panelTop);
        }

        private void LoadData(string filter = "")
        {
            var list = _memoryManager.GetAllKnowledge();
            if (!string.IsNullOrWhiteSpace(filter))
            {
                list = list.FindAll(x =>
                    (x.IntentPattern != null && x.IntentPattern.Contains(filter)) ||
                    (x.TargetKeywords != null && x.TargetKeywords.Contains(filter)) ||
                    (x.Description != null && x.Description.Contains(filter))
                );
            }

            dgvKnowledge.DataSource = null;
            dgvKnowledge.DataSource = list;

            if (dgvKnowledge.Columns["Id"] != null) dgvKnowledge.Columns["Id"].HeaderText = "شناسه";
            if (dgvKnowledge.Columns["IntentPattern"] != null) dgvKnowledge.Columns["IntentPattern"].HeaderText = "الگو / قصد کاربر";
            if (dgvKnowledge.Columns["TargetDomain"] != null) dgvKnowledge.Columns["TargetDomain"].HeaderText = "دامنه وب‌سایت";
            if (dgvKnowledge.Columns["TargetUrl"] != null) dgvKnowledge.Columns["TargetUrl"].HeaderText = "آدرس صفحه";
            if (dgvKnowledge.Columns["ElementSelector"] != null) dgvKnowledge.Columns["ElementSelector"].HeaderText = "انتخاب‌گر دکمه";
            if (dgvKnowledge.Columns["TargetKeywords"] != null) dgvKnowledge.Columns["TargetKeywords"].HeaderText = "کلیدواژه‌های دکمه";
            if (dgvKnowledge.Columns["ActionType"] != null) dgvKnowledge.Columns["ActionType"].HeaderText = "نوع عملیات";
            if (dgvKnowledge.Columns["Description"] != null) dgvKnowledge.Columns["Description"].HeaderText = "توضیحات دانش";
            if (dgvKnowledge.Columns["CreatedAt"] != null) dgvKnowledge.Columns["CreatedAt"].HeaderText = "تاریخ ثبت";
            if (dgvKnowledge.Columns["UpdatedAt"] != null) dgvKnowledge.Columns["UpdatedAt"].Visible = false;
        }

        private void TxtSearch_TextChanged(object sender, EventArgs e)
        {
            LoadData(txtSearch.Text);
        }

        private void BtnAdd_Click(object sender, EventArgs e)
        {
            using (var prompt = new AddEditKnowledgeDialog())
            {
                if (prompt.ShowDialog() == DialogResult.OK)
                {
                    _memoryManager.SaveKnowledge(prompt.KnowledgeItem);
                    LoadData();
                    MessageBox.Show("قاعده جدید با موفقیت اضافه شد.", "موفقیت", MessageBoxButtons.OK, MessageBoxIcon.Information);
                }
            }
        }

        private void BtnEdit_Click(object sender, EventArgs e)
        {
            if (dgvKnowledge.SelectedRows.Count == 0) return;
            var selected = (LearnedKnowledge)dgvKnowledge.SelectedRows[0].DataBoundItem;

            using (var prompt = new AddEditKnowledgeDialog(selected))
            {
                if (prompt.ShowDialog() == DialogResult.OK)
                {
                    _memoryManager.UpdateKnowledge(prompt.KnowledgeItem);
                    LoadData();
                    MessageBox.Show("قاعده با موفقیت به‌روزرسانی شد.", "موفقیت", MessageBoxButtons.OK, MessageBoxIcon.Information);
                }
            }
        }

        private void BtnDelete_Click(object sender, EventArgs e)
        {
            if (dgvKnowledge.SelectedRows.Count == 0) return;
            var selected = (LearnedKnowledge)dgvKnowledge.SelectedRows[0].DataBoundItem;

            if (MessageBox.Show($"آیا از حذف قاعده '{selected.IntentPattern}' اطمینان دارید؟", "تایید حذف", MessageBoxButtons.YesNo, MessageBoxIcon.Warning) == DialogResult.Yes)
            {
                _memoryManager.DeleteKnowledge(selected.Id);
                LoadData();
            }
        }
    }

    public class AddEditKnowledgeDialog : Form
    {
        public LearnedKnowledge KnowledgeItem { get; private set; }

        private TextBox txtIntent;
        private TextBox txtDomain;
        private TextBox txtKeywords;
        private TextBox txtSelector;
        private TextBox txtDescription;
        private Button btnSave;
        private Button btnCancel;

        public AddEditKnowledgeDialog(LearnedKnowledge existing = null)
        {
            KnowledgeItem = existing ?? new LearnedKnowledge();
            InitializeDialog(existing != null);
        }

        private void InitializeDialog(bool isEdit)
        {
            this.Text = isEdit ? "ویرایش قاعده آموخته‌شده" : "افزودن قاعده جدید به حافظه هوش مصنوعی";
            this.Size = new Size(500, 380);
            this.RightToLeft = RightToLeft.Yes;
            this.RightToLeftLayout = true;
            this.StartPosition = FormStartPosition.CenterParent;
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;

            int lblX = 350, inputX = 30, width = 300;

            var lblIntent = new Label { Text = "الگو / قصد کاربر:", Location = new Point(lblX, 20), AutoSize = true };
            txtIntent = new TextBox { Location = new Point(inputX, 18), Width = width, Text = KnowledgeItem.IntentPattern };

            var lblDomain = new Label { Text = "دامنه سایت (* برای همه):", Location = new Point(lblX, 60), AutoSize = true };
            txtDomain = new TextBox { Location = new Point(inputX, 58), Width = width, Text = string.IsNullOrEmpty(KnowledgeItem.TargetDomain) ? "*" : KnowledgeItem.TargetDomain };

            var lblKeywords = new Label { Text = "کلمات روی دکمه (کاما جدا):", Location = new Point(lblX, 100), AutoSize = true };
            txtKeywords = new TextBox { Location = new Point(inputX, 98), Width = width, Text = KnowledgeItem.TargetKeywords };

            var lblSelector = new Label { Text = "انتخاب‌گر CSS/Text:", Location = new Point(lblX, 140), AutoSize = true };
            txtSelector = new TextBox { Location = new Point(inputX, 138), Width = width, Text = KnowledgeItem.ElementSelector };

            var lblDesc = new Label { Text = "توضیحات و یادداشت:", Location = new Point(lblX, 180), AutoSize = true };
            txtDescription = new TextBox { Location = new Point(inputX, 178), Width = width, Multiline = true, Height = 60, Text = KnowledgeItem.Description };

            btnSave = new Button { Text = "💾 ذخیره", Location = new Point(180, 260), Width = 100, Height = 35, DialogResult = DialogResult.OK, BackColor = Color.FromArgb(46, 204, 113), ForeColor = Color.White };
            btnSave.Click += BtnSave_Click;

            btnCancel = new Button { Text = "انصراف", Location = new Point(60, 260), Width = 100, Height = 35, DialogResult = DialogResult.Cancel };

            this.Controls.Add(lblIntent); this.Controls.Add(txtIntent);
            this.Controls.Add(lblDomain); this.Controls.Add(txtDomain);
            this.Controls.Add(lblKeywords); this.Controls.Add(txtKeywords);
            this.Controls.Add(lblSelector); this.Controls.Add(txtSelector);
            this.Controls.Add(lblDesc); this.Controls.Add(txtDescription);
            this.Controls.Add(btnSave); this.Controls.Add(btnCancel);
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            KnowledgeItem.IntentPattern = txtIntent.Text;
            KnowledgeItem.TargetDomain = txtDomain.Text;
            KnowledgeItem.TargetKeywords = txtKeywords.Text;
            KnowledgeItem.ElementSelector = txtSelector.Text;
            KnowledgeItem.Description = txtDescription.Text;
            KnowledgeItem.ActionType = "Click";
            KnowledgeItem.UpdatedAt = DateTime.Now;
        }
    }
}
