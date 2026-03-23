# 📡 RSS Feed Reader with Social Platform Filter (CodeIgniter 3)

## 🚀 Overview

This project is a **RSS Feed Reader** built using **CodeIgniter 3**, featuring:

* 📥 Import RSS Feed from URL
* 🧠 Auto-fetch title, description, and image
* 🧩 Social platform-based filtering (Facebook, Twitter, etc.)
* 🧱 Modern **Card UI with large icons (no images)**
* ⚡ AJAX-based pagination & filtering
* 🚫 Smart empty state handling

---

## 🛠️ Tech Stack

* PHP (CodeIgniter 3)
* MySQL
* jQuery (AJAX)
* Bootstrap (UI)
* Font Awesome (Icons)

---

---

## ⚙️ Features

### 1️⃣ RSS Feed Import

* Input RSS URL
* Fetch feed data
* Store in database
* If image missing → scrape first `<img>` from page

---

### 2️⃣ Social Platform Filter (Card UI)

* Big icon-based cards (no images)
* Click → filter posts
* Active selection highlight

```html
<i class="fa-brands fa-facebook"></i>
```

---

### 3️⃣ AJAX Post Loading

```javascript
function loadPosts(page = 1, platform_id = null) {
    $.post("fetchPosts", {
        page: page,
        platform_id: platform_id
    }, function (res) {
        $("#post-container").html(res.html);
    });
}
```

---

### 4️⃣ Pagination + State Management

* Maintains selected platform
* Loads posts without refresh

---

### 5️⃣ Empty State Handling

| Condition             | Message                      |
| --------------------- | ---------------------------- |
| No posts in DB        | "No Posts Yet"               |
| No posts for platform | "No posts for this platform" |
| No results            | "No data found"              |

---

## 🗄️ Database Schema

### `posts`

| Field       | Type     |
| ----------- | -------- |
| id          | INT      |
| title       | TEXT     |
| description | TEXT     |
| image       | TEXT     |
| link        | TEXT     |
| platform_id | INT      |
| created_at  | DATETIME |

---

### `social_platforms`

| Field      | Type    |
| ---------- | ------- |
| id         | INT     |
| name       | VARCHAR |
| icon_class | VARCHAR |

Example:

```
Facebook → fa-brands fa-facebook
Twitter → fa-brands fa-x-twitter
```

---

## 🔌 API / Routes

| Route       | Method | Description         |
| ----------- | ------ | ------------------- |
| /fetchPosts | POST   | Load posts via AJAX |
| /importFeed | POST   | Import RSS feed     |

---

## 🎯 Key Functions

### Load Posts (Controller)

```php
public function fetchPosts()
{
    $page = $this->input->post('page');
    $platform_id = $this->input->post('platform_id');

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $data['posts'] = $this->Post_model->get_posts($limit, $offset, $platform_id);

    echo json_encode([
        'html' => $this->load->view('posts/card_view', $data, true),
        'total' => count($data['posts'])
    ]);
}
```

---

## 🎨 UI Highlights

* Big clickable icon cards
* Smooth hover effects
* Active state selection
* Responsive layout

---

## ⚡ Setup Instructions

1. Clone project

```
git clone <your-repo-url>
```

2. Move to project folder

```
cd project-folder
```

3. Configure database in:

```
application/config/database.php
```

4. Run project:

```
http://localhost/project
```

---

## 🔥 Future Improvements

* Infinite scroll
* Multi-platform filter
* Drag & sort priority
* AI-based content tagging

---
##migration and seed
http://localhost/rssfeedreader/migration/
http://localhost/rssfeedreader/seeder/
## 👨‍💻 Author

**Aniruddha Das**
Senior Web Developer (PHP | Node.js | Laravel | AI)

---

## 📜 License

This project is open-source and free to use.
