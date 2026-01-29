# Laravel Workshop：尾牙賭盤下注系統 🎲

本 Workshop 目標是用 Laravel 完整走一遍：

1. DB Migration  
2. Models  
3. Controller  
4. View (Blade)

最後做出一個可以下注 + 查看下注列表的尾牙賭盤系統。

---

## 🎯 功能需求

### 資料表

#### members
| 欄位 | 型別 |
|------|------|
| id | bigint |
| name | string |
| created_at | timestamp |
| updated_at | timestamp |

#### gamblings
| 欄位 | 型別 |
|------|------|
| id | bigint |
| applicant | string |
| created_at | timestamp |
| updated_at | timestamp |

#### gambling_papa (Game1 明細)
| 欄位 | 型別 |
|------|------|
| gambling_id | bigint |
| member_id | bigint |

> Game1：可以勾選 5 個人

#### gambling_line (Game2 明細)
| 欄位 | 型別 |
|------|------|
| gambling_id | bigint |
| member_id | bigint |
| seq | tinyint |

> Game2：依序選擇 3 格人（seq = 1~3）

---

## 🕹️ 遊戲規則

### Game 1：尾牙斷線趴趴熊
- 勾選 **5 個人**
- 寫入 `gambling_papa`

### Game 2：尾牙醉失憶連線
- 依序選擇 **3 格人**
- 寫入 `gambling_line`，並記錄 `seq`

---

## 📄 頁面需求

### 1) 下注頁
路由：`GET /gamblings/create`

- 輸入 applicant（下注人）
- Game1 勾選 5 人
- Game2 依序選 3 人（第一格、第二格、第三格）
- 送出後建立一筆下注

### 2) 賭盤列表頁
路由：`GET /gamblings`

- 顯示每一筆下注紀錄
- Game1 選了哪 5 人
- Game2 seq1~3 各選了誰

---