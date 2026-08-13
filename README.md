# security-app-practice

## 概要

COACHTECH 教材 Tutorial 10-6「Webセキュリティ ハンズオン」で作成した成果物です。<br>
**セキュリティを意識したお問い合わせフォーム**

## 使用技術

- PHP 8.x
- Laravel 10.x
- CSRF 保護（`@csrf`）、XSS 対策（Blade の自動エスケープ）

## 学んだこと

# Laravel ルーティング：URL・ルート名・メソッド名を分けて考える

## 1. 最も重要なポイント

Laravelのルーティングでは、次の3つを**別々のもの**として考える。

| 項目                   | 例             | 役割                                    |
| ---------------------- | -------------- | --------------------------------------- |
| URL                    | `/contact`     | ブラウザからアクセスする住所            |
| Controllerのメソッド名 | `form()`       | URLにアクセスされたときに実行する処理   |
| ルート名               | `contact.form` | Laravel内部でルートを呼び出すための名前 |

例えば、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');
```

は、次のように分解して考える。

```text
Route::get(
    '/contact',                        ← URL
    [ContactController::class, 'form'] ← Controllerとメソッド
)
->name('contact.form');                ← ルート名
```

---

# 2. URLとは？

URLは、**ブラウザからアクセスする住所**。

例えば、

```text
/contact
```

なら、ブラウザから、

```text
http://localhost/contact
```

などでアクセスする。

Laravelでは、

```php
Route::get('/contact', ...);
```

の、

```php
'/contact'
```

がURLにあたる。

---

# 3. Controllerのメソッド名とは？

Controllerのメソッドは、

> 「そのURLにアクセスされたとき、どの処理を実行するか」

を指定するもの。

例えば、

```php
Route::get('/contact', [ContactController::class, 'form']);
```

なら、

```text
ブラウザ
    ↓
GET /contact
    ↓
ContactController
    ↓
form()を実行
```

となる。

Controllerには、

```php
class ContactController extends Controller
{
    public function form()
    {
        return view('contact.form');
    }
}
```

と書く。

つまり、

```php
'form'
```

は、

```php
public function form()
```

を実行するための指定。

---

# 4. URLとメソッド名は同じでなくていい

ここが非常に重要。

例えば、

```php
Route::get('/contact', [ContactController::class, 'form']);
```

の場合、

```text
URL
/contact

Controller
ContactController

メソッド
form()
```

となる。

「`form()`だからURLは `/contact/form` なのでは？」

と思いやすいが、**そんな決まりはない。**

URLとメソッド名は別々に自由に決められる。

例えば、

```php
Route::get('/contact', [ContactController::class, 'form']);
```

でもいいし、

```php
Route::get('/contact/form', [ContactController::class, 'form']);
```

でもいい。

さらに、

```php
Route::get('/inquiry', [ContactController::class, 'form']);
```

でもいい。

すべて、

```text
指定したURL
    ↓
指定したControllerのメソッド
```

という関係になる。

---

# 5. `/contact` と `/contact/form` の違い

例えば、

```php
Route::get('/contact', [ContactController::class, 'form']);
```

なら、

```text
/contact
```

にアクセスすると、

```php
ContactController::form()
```

が実行される。

一方、

```php
Route::get('/contact/form', [ContactController::class, 'form']);
```

なら、

```text
/contact/form
```

にアクセスすると、

```php
ContactController::form()
```

が実行される。

つまり、

| URL             | 実行されるメソッド |
| --------------- | ------------------ |
| `/contact`      | `form()`           |
| `/contact/form` | `form()`           |
| `/inquiry`      | `form()`           |
| `/abc`          | `form()`           |

のように、**URLとメソッド名には直接的な決まりはない。**

---

# 6. なぜお問い合わせフォームを `/contact` にするのか？

これはURLを分かりやすく設計しているため。

```text
/contact
```

を見れば、

> 「お問い合わせページだな」

と分かる。

そのページを開いたら、お問い合わせフォームが表示される。

```text
GET /contact
    ↓
ContactController::form()
    ↓
お問い合わせフォームを表示
```

そのため、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');
```

という設計にしている。

---

# 7. `/contact/thanks` はなぜ必要？

お問い合わせを送信した後には、

```text
お問い合わせありがとうございました
```

という完了ページを表示したい。

そこで、

```text
/contact/thanks
```

という別のURLを用意する。

```php
Route::get('/contact/thanks', [ContactController::class, 'thanks'])
    ->name('contact.thanks');
```

流れは、

```text
GET /contact
    ↓
お問い合わせフォーム
    ↓
ユーザーが入力
    ↓
POST /contact
    ↓
送信処理
    ↓
GET /contact/thanks
    ↓
送信完了画面
```

となる。

---

# 8. `/contact` と `/contact/thanks` の関係

URLを階層的に考えると分かりやすい。

```text
/contact
    ↑
お問い合わせ機能の入口

/contact/thanks
    ↑
お問い合わせ機能の完了ページ
```

`/contact/thanks` の `contact` は、

> 「お問い合わせ関連のページですよ」

という意味でURLを整理している。

例えば同じような設計は、

```text
/products
/products/123

/users
/users/123

/blog
/blog/123
```

などでも使われる。

---

# 9. ルート名とは？

例えば、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');
```

の、

```php
->name('contact.form')
```

は**URLではない**。

これはLaravel内部でこのルートを識別するための「名前」。

つまり、

```text
URL
/contact

ルート名
contact.form
```

は別物。

---

# 10. ルート名はURLの代わりに使える

例えばBladeで、

```blade
<a href="{{ route('contact.form') }}">
    お問い合わせ
</a>
```

と書く。

Laravelは、

```text
route('contact.form')
        ↓
contact.formという名前のルートを探す
        ↓
URLは何？
        ↓
/contact
```

と判断する。

その結果、ブラウザには、

```html
<a href="/contact"> お問い合わせ </a>
```

のようなリンクが生成される。

---

# 11. なぜURLを直接書かずにルート名を使うのか？

例えば、

```blade
<a href="/contact">お問い合わせ</a>
```

と直接URLを書くこともできる。

しかし、ルート名を使って、

```blade
<a href="{{ route('contact.form') }}">
    お問い合わせ
</a>
```

と書くと、URLを変更したときに便利。

例えば、

```php
Route::get('/contact', ...)
```

を、

```php
Route::get('/inquiry', ...)
```

に変更したとする。

ルート名が、

```php
->name('contact.form')
```

のままなら、

```blade
route('contact.form')
```

を使っている箇所は基本的にそのままでよい。

Laravelが、

```text
contact.form
    ↓
現在のURL
    ↓
/inquiry
```

と解決してくれる。

---

# 12. `name('contact.form')` の「form」は何？

ここも混乱しやすい。

```php
->name('contact.form')
```

の `contact.form` は、あくまで**ルート名**。

Controllerの、

```php
public function form()
```

とは別物。

たまたま、

```text
contact.form
      ↑
     form
```

と、

```php
form()
```

の名前を合わせているだけ。

---

# 13. 3つを完全に分離して考える

例えば、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');
```

なら、

```text
┌──────────────────────────┐
│ URL                      │
│ /contact                 │
│                          │
│ ブラウザからアクセスする住所 │
└────────────┬─────────────┘
             ↓
┌──────────────────────────┐
│ Controller               │
│ ContactController        │
└────────────┬─────────────┘
             ↓
┌──────────────────────────┐
│ メソッド                 │
│ form()                   │
│                          │
│ 実際に実行する処理       │
└──────────────────────────┘

ルート名
contact.form

Laravel内部でルートを呼び出すための名前
```

つまり、

```text
URL
/contact

メソッド
form()

ルート名
contact.form
```

は**全部別物**。

---

# 14. GETとPOSTも重要

Laravelでは、

```php
Route::get(...)
```

と、

```php
Route::post(...)
```

も区別される。

例えば、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');

Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');
```

この2つはURLが同じ、

```text
/contact
```

だが、HTTPメソッドが違う。

```text
GET /contact
    ↓
form()
    ↓
フォームを表示
```

一方、

```text
POST /contact
    ↓
store()
    ↓
入力されたデータを処理
```

となる。

---

# 15. なぜ同じ `/contact` なのに別の処理ができるのか？

Laravelは、

```text
HTTPメソッド + URL
```

の組み合わせでルートを判断する。

つまり、

```text
GET + /contact
```

と、

```text
POST + /contact
```

は別のルートとして扱われる。

そのため、

```php
Route::get('/contact', [ContactController::class, 'form']);
```

と、

```php
Route::post('/contact', [ContactController::class, 'store']);
```

を同時に定義できる。

---

# 16. お問い合わせフォーム全体の流れ

今回作成しているお問い合わせフォームを全体で見ると、

```text
① ブラウザ
   ↓
GET /contact
   ↓
ContactController
   ↓
form()
   ↓
contact.form.blade.php
   ↓
お問い合わせフォーム表示
```

ユーザーが入力して送信すると、

```text
② フォーム送信
   ↓
POST /contact
   ↓
ContactController
   ↓
store()
   ↓
入力内容を処理
```

その後、

```text
③ 送信完了
   ↓
GET /contact/thanks
   ↓
ContactController
   ↓
thanks()
   ↓
contact.thanks.blade.php
   ↓
「送信完了」画面
```

という流れになる。

---

# 17. 今回の完成形

`routes/web.php`

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

// お問い合わせフォーム表示
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');

// お問い合わせ送信処理
Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

// 送信完了画面
Route::get('/contact/thanks', [ContactController::class, 'thanks'])
    ->name('contact.thanks');
```

それぞれを表にすると、

| HTTPメソッド | URL               | Controller          | メソッド   | ルート名         | 役割                 |
| ------------ | ----------------- | ------------------- | ---------- | ---------------- | -------------------- |
| GET          | `/contact`        | `ContactController` | `form()`   | `contact.form`   | フォーム表示         |
| POST         | `/contact`        | `ContactController` | `store()`  | `contact.store`  | 入力内容を送信・処理 |
| GET          | `/contact/thanks` | `ContactController` | `thanks()` | `contact.thanks` | 完了画面表示         |

---

# 18. 最重要：3つを混同しない

Laravelのルーティングで混乱したら、まず次の3つを分けて考える。

```text
① URL
   ↓
ブラウザの住所

② Controllerのメソッド
   ↓
実際に実行する処理

③ ルート名
   ↓
Laravel内部でルートを呼び出すための名前
```

例えば、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');
```

を見たら、

```text
'/contact'
    ↓
「どこにアクセスする？」

form()
    ↓
「何の処理をする？」

contact.form
    ↓
「Laravel内部でこのルートを何という名前で呼ぶ？」
```

と考える。

---

# 19. 覚え方

次のように覚えると分かりやすい。

```text
URL
＝ ブラウザの住所

メソッド
＝ その住所に来たときに何をするか

ルート名
＝ Laravel内部でのそのルートの名前
```

つまり、

> **「住所」「処理」「ルートの名前」は別々。**

これがLaravelルーティングを理解するうえで非常に重要。

---

# 20. `php artisan route:list` で確認する

現在Laravelに登録されているルートは、

```bash
php artisan route:list
```

で確認できる。

例えば、

```text
GET|HEAD   contact
POST       contact
GET|HEAD   contact/thanks
```

などが表示される。

「自分がブラウザでアクセスしているURLに対応するルートが本当に登録されているか？」

を確認したいときに便利。

特に404が出たときは、

```bash
php artisan route:list
```

を実行して、

```text
HTTPメソッド
URL
Controller
```

を確認すると、原因を見つけやすい。

---

# まとめ

Laravelのルーティングでは、

```php
Route::get('/contact', [ContactController::class, 'form'])
    ->name('contact.form');
```

を見たら、次のように読む。

```text
Route::get
    ↓
GETでアクセスされたら

'/contact'
    ↓
このURLにアクセスされたら

ContactController
    ↓
このControllerの

'form'
    ↓
form()メソッドを実行する

->name('contact.form')
    ↓
このルートを「contact.form」という名前で登録する
```

**「`form()`だから `/contact/form`」ではない。**

**「`contact.form`だから `/contact/form`」でもない。**

URLはURL、メソッド名はメソッド名、ルート名はルート名。

**この3つは独立している。**

この考え方が身につけば、Laravelのルーティングコードをかなりスムーズに読めるようになる。

-
-

## 動作確認

- 動作確認URL: http://localhost/contact
- フォームの表示：GETルートの確認
- 必須項目を空にして送信：バリデーションチェックで引っかかる
- 正しく入力して送信→完了画面が表示<br>

**@csrf保護の確認**<br>

- @csrfを削除して、登録フォームを送信すると、「419 Page Expired」エラーが発生する。
- HTMLソース確認（右クリック→ページのソースを表示）<br>

**XSS対策の確認**<br>

- HTMLソース確認（右クリック→ページのソースを表示）：<script>が&lt;script&gt;にエスケープ
- 完了画面で、スクリプトが実行されず、文字列として表示（色がついていない）
