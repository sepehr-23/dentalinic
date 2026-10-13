using System;
using System.Collections.Generic;
using System.Data.SQLite;
using System.IO;
using AIBrowserAssistant.Models;

namespace AIBrowserAssistant.Database
{
    public class MemoryManager
    {
        private readonly string _dbPath;
        private readonly string _connectionString;

        public MemoryManager(string dbFileName = "ai_knowledge.db")
        {
            _dbPath = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, dbFileName);
            _connectionString = $"Data Source={_dbPath};Version=3;";
            InitializeDatabase();
        }

        private void InitializeDatabase()
        {
            if (!File.Exists(_dbPath))
            {
                SQLiteConnection.CreateFile(_dbPath);
            }

            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();

                string createActionLogsTable = @"
                    CREATE TABLE IF NOT EXISTS ActionLogs (
                        Id INTEGER PRIMARY KEY AUTOINCREMENT,
                        Domain TEXT,
                        Url TEXT,
                        ActionType TEXT,
                        ElementTagName TEXT,
                        ElementId TEXT,
                        ElementText TEXT,
                        CssSelector TEXT,
                        XPath TEXT,
                        ExtraData TEXT,
                        RecordedAt DATETIME DEFAULT CURRENT_TIMESTAMP
                    );";

                string createKnowledgeTable = @"
                    CREATE TABLE IF NOT EXISTS LearnedKnowledge (
                        Id INTEGER PRIMARY KEY AUTOINCREMENT,
                        IntentPattern TEXT,
                        TargetDomain TEXT,
                        TargetUrl TEXT,
                        ElementSelector TEXT,
                        TargetKeywords TEXT,
                        ActionType TEXT,
                        Description TEXT,
                        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                        UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
                    );";

                using (var cmd = new SQLiteCommand(createActionLogsTable, conn))
                {
                    cmd.ExecuteNonQuery();
                }

                using (var cmd = new SQLiteCommand(createKnowledgeTable, conn))
                {
                    cmd.ExecuteNonQuery();
                }

                // Inject default Persian day mappings if table is empty
                SeedDefaultKnowledge(conn);
            }
        }

        private void SeedDefaultKnowledge(SQLiteConnection conn)
        {
            string countQuery = "SELECT COUNT(*) FROM LearnedKnowledge;";
            using (var cmd = new SQLiteCommand(countQuery, conn))
            {
                long count = (long)cmd.ExecuteScalar();
                if (count == 0)
                {
                    string insertDefaults = @"
                        INSERT INTO LearnedKnowledge (IntentPattern, TargetDomain, TargetUrl, ElementSelector, TargetKeywords, ActionType, Description)
                        VALUES
                        ('چهارشنبه', '*', '*', 'text:wed,wednesday,چهارشنبه', 'wed,wednesday,4shanbe,چهارشنبه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک چهارشنبه'),
                        ('جمعه', '*', '*', 'text:fri,friday,جمعه', 'fri,friday,jomeh,جمعه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک جمعه'),
                        ('شنبه', '*', '*', 'text:sat,saturday,شنبه', 'sat,saturday,shanbe,شنبه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک شنبه'),
                        ('یکشنبه', '*', '*', 'text:sun,sunday,یکشنبه', 'sun,sunday,yakshanbe,یکشنبه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک یکشنبه'),
                        ('دوشنبه', '*', '*', 'text:mon,monday,دوشنبه', 'mon,monday,doshanbe,دوشنبه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک دوشنبه'),
                        ('سه شنبه', '*', '*', 'text:tue,tuesday,سه شنبه', 'tue,tuesday,3shanbe,سه شنبه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک سه شنبه'),
                        ('پنجشنبه', '*', '*', 'text:thu,thursday,پنجشنبه', 'thu,thursday,5shanbe,پنجشنبه', 'Click', 'قاعده پیش‌فرض برای یافتن دکمه یا لینک پنجشنبه');
                    ";
                    using (var insertCmd = new SQLiteCommand(insertDefaults, conn))
                    {
                        insertCmd.ExecuteNonQuery();
                    }
                }
            }
        }

        public void SaveActionLog(ActionLog log)
        {
            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();
                string query = @"
                    INSERT INTO ActionLogs (Domain, Url, ActionType, ElementTagName, ElementId, ElementText, CssSelector, XPath, ExtraData)
                    VALUES (@Domain, @Url, @ActionType, @ElementTagName, @ElementId, @ElementText, @CssSelector, @XPath, @ExtraData);";

                using (var cmd = new SQLiteCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@Domain", log.Domain ?? "");
                    cmd.Parameters.AddWithValue("@Url", log.Url ?? "");
                    cmd.Parameters.AddWithValue("@ActionType", log.ActionType ?? "Click");
                    cmd.Parameters.AddWithValue("@ElementTagName", log.ElementTagName ?? "");
                    cmd.Parameters.AddWithValue("@ElementId", log.ElementId ?? "");
                    cmd.Parameters.AddWithValue("@ElementText", log.ElementText ?? "");
                    cmd.Parameters.AddWithValue("@CssSelector", log.CssSelector ?? "");
                    cmd.Parameters.AddWithValue("@XPath", log.XPath ?? "");
                    cmd.Parameters.AddWithValue("@ExtraData", log.ExtraData ?? "");
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public List<ActionLog> GetRecentLogs(int limit = 50)
        {
            var list = new List<ActionLog>();
            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();
                string query = "SELECT * FROM ActionLogs ORDER BY Id DESC LIMIT @Limit;";
                using (var cmd = new SQLiteCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@Limit", limit);
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            list.Add(new ActionLog
                            {
                                Id = Convert.ToInt32(reader["Id"]),
                                Domain = reader["Domain"].ToString(),
                                Url = reader["Url"].ToString(),
                                ActionType = reader["ActionType"].ToString(),
                                ElementTagName = reader["ElementTagName"].ToString(),
                                ElementId = reader["ElementId"].ToString(),
                                ElementText = reader["ElementText"].ToString(),
                                CssSelector = reader["CssSelector"].ToString(),
                                XPath = reader["XPath"].ToString(),
                                ExtraData = reader["ExtraData"].ToString(),
                                RecordedAt = reader["RecordedAt"].ToString()
                            });
                        }
                    }
                }
            }
            return list;
        }

        public void SaveKnowledge(LearnedKnowledge knowledge)
        {
            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();
                string query = @"
                    INSERT INTO LearnedKnowledge (IntentPattern, TargetDomain, TargetUrl, ElementSelector, TargetKeywords, ActionType, Description)
                    VALUES (@IntentPattern, @TargetDomain, @TargetUrl, @ElementSelector, @TargetKeywords, @ActionType, @Description);";

                using (var cmd = new SQLiteCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@IntentPattern", knowledge.IntentPattern ?? "");
                    cmd.Parameters.AddWithValue("@TargetDomain", knowledge.TargetDomain ?? "*");
                    cmd.Parameters.AddWithValue("@TargetUrl", knowledge.TargetUrl ?? "*");
                    cmd.Parameters.AddWithValue("@ElementSelector", knowledge.ElementSelector ?? "");
                    cmd.Parameters.AddWithValue("@TargetKeywords", knowledge.TargetKeywords ?? "");
                    cmd.Parameters.AddWithValue("@ActionType", knowledge.ActionType ?? "Click");
                    cmd.Parameters.AddWithValue("@Description", knowledge.Description ?? "");
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public List<LearnedKnowledge> GetAllKnowledge()
        {
            var list = new List<LearnedKnowledge>();
            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();
                string query = "SELECT * FROM LearnedKnowledge ORDER BY Id DESC;";
                using (var cmd = new SQLiteCommand(query, conn))
                {
                    using (var reader = cmd.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            list.Add(new LearnedKnowledge
                            {
                                Id = Convert.ToInt32(reader["Id"]),
                                IntentPattern = reader["IntentPattern"].ToString(),
                                TargetDomain = reader["TargetDomain"].ToString(),
                                TargetUrl = reader["TargetUrl"].ToString(),
                                ElementSelector = reader["ElementSelector"].ToString(),
                                TargetKeywords = reader["TargetKeywords"].ToString(),
                                ActionType = reader["ActionType"].ToString(),
                                Description = reader["Description"].ToString(),
                                CreatedAt = reader["CreatedAt"] != DBNull.Value ? Convert.ToDateTime(reader["CreatedAt"]) : DateTime.Now
                            });
                        }
                    }
                }
            }
            return list;
        }

        public void DeleteKnowledge(int id)
        {
            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();
                string query = "DELETE FROM LearnedKnowledge WHERE Id = @Id;";
                using (var cmd = new SQLiteCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@Id", id);
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public void UpdateKnowledge(LearnedKnowledge k)
        {
            using (var conn = new SQLiteConnection(_connectionString))
            {
                conn.Open();
                string query = @"
                    UPDATE LearnedKnowledge
                    SET IntentPattern = @IntentPattern,
                        TargetDomain = @TargetDomain,
                        TargetUrl = @TargetUrl,
                        ElementSelector = @ElementSelector,
                        TargetKeywords = @TargetKeywords,
                        ActionType = @ActionType,
                        Description = @Description,
                        UpdatedAt = CURRENT_TIMESTAMP
                    WHERE Id = @Id;";

                using (var cmd = new SQLiteCommand(query, conn))
                {
                    cmd.Parameters.AddWithValue("@Id", k.Id);
                    cmd.Parameters.AddWithValue("@IntentPattern", k.IntentPattern ?? "");
                    cmd.Parameters.AddWithValue("@TargetDomain", k.TargetDomain ?? "*");
                    cmd.Parameters.AddWithValue("@TargetUrl", k.TargetUrl ?? "*");
                    cmd.Parameters.AddWithValue("@ElementSelector", k.ElementSelector ?? "");
                    cmd.Parameters.AddWithValue("@TargetKeywords", k.TargetKeywords ?? "");
                    cmd.Parameters.AddWithValue("@ActionType", k.ActionType ?? "Click");
                    cmd.Parameters.AddWithValue("@Description", k.Description ?? "");
                    cmd.ExecuteNonQuery();
                }
            }
        }

        public LearnedKnowledge FindMatchingKnowledge(string userQuery, string currentDomain)
        {
            var all = GetAllKnowledge();
            userQuery = userQuery.ToLower();

            foreach (var item in all)
            {
                if (item.TargetDomain != "*" && !string.IsNullOrEmpty(currentDomain) && !currentDomain.Contains(item.TargetDomain))
                {
                    continue;
                }

                if (!string.IsNullOrEmpty(item.IntentPattern) && userQuery.Contains(item.IntentPattern.ToLower()))
                {
                    return item;
                }

                if (!string.IsNullOrEmpty(item.TargetKeywords))
                {
                    var keywords = item.TargetKeywords.Split(new[] { ',', ' ', ';' }, StringSplitOptions.RemoveEmptyEntries);
                    foreach (var kw in keywords)
                    {
                        if (userQuery.Contains(kw.Trim().ToLower()))
                        {
                            return item;
                        }
                    }
                }
            }

            return null;
        }
    }
}
