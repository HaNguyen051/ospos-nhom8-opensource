# OSPOS cho quán cà phê: hướng dẫn tiếng Việt

Tài liệu này giải thích dự án, cách cài đặt bằng Docker trên Windows, cấu trúc mã nguồn và các lỗi thường gặp. README gốc của OSPOS (tiếng Anh) vẫn giữ nguyên ở [README.md](README.md).

## 1. Dự án là gì

Đồ án môn Phần mềm mã nguồn mở của Nhóm 8: phát triển thêm trên **OpenSourcePOS (OSPOS)** để thành hệ thống bán hàng cho quán cà phê.

- OSPOS là phần mềm bán hàng chạy trên web, giấy phép MIT, miễn phí.
- Công nghệ: PHP 8.2+, CodeIgniter 4, MySQL/MariaDB, Bootstrap 3, jQuery.
- Mã nguồn gốc: https://github.com/opensourcepos/opensourcepos
- Repo của nhóm: https://github.com/HaNguyen051/ospos-nhom8-opensource

### OSPOS đã có sẵn

Bán hàng và thanh toán, quản lý món (module Items), bàn ăn (Dinner tables), in hóa đơn, nhân viên và phân quyền, báo cáo, đa ngôn ngữ (có tiếng Việt).

### Nhóm làm thêm

Sơ đồ bàn trực quan, chuyển bàn, gộp bàn; ghi chú món và chọn size; mẫu hóa đơn quán cà phê; báo cáo doanh thu theo ngày và món bán chạy. Nâng cao: khách quét QR tại bàn để gọi món.

Chi tiết tiến độ xem trong [tiendo.html](tiendo.html).

## 2. Giấy phép: điều cần nhớ

Giấy phép là MIT kèm điều kiện thêm của OSPOS:

- Dòng bản quyền ở **chân trang** (`© 2010 - năm hiện tại · opensourcepos.org · 3.x.x - mã commit`) **phải giữ nguyên, hiện trên mọi trang và không được sửa**.
- Không xóa file `LICENSE` và các file giấy phép.
- Không thay chân trang bằng thông tin của nhóm, không nhận là chủ sở hữu mã nguồn.

Phần nhóm làm thêm thì ghi rõ trong báo cáo là "phát triển trên nền OSPOS".

## 3. Cài đặt bằng Docker (Windows)

### 3.1. Cần có

| Công cụ | Ghi chú |
| --- | --- |
| Docker Desktop | Bật trước khi chạy, chờ trạng thái "Engine running". Cần WSL2. Nên dùng bản mới (file override dùng cú pháp `!override`, cần Docker Compose từ 2.24). |
| Node.js 22 trở lên | Để build giao diện. |
| Git | Để clone repo. |

Không cần cài PHP, Composer hay MySQL lên máy: tất cả chạy trong Docker.

Không dùng XAMPP bản cũ. OSPOS cần PHP 8.2 trở lên kèm extension `intl` và `gd`.

### 3.2. Các bước

Chạy trong PowerShell, tại thư mục dự án.

**Bước 1. Clone repo**

```powershell
git clone https://github.com/HaNguyen051/ospos-nhom8-opensource.git
cd ospos-nhom8-opensource
```

**Bước 2. Cài thư viện PHP** (dùng image Composer chính thức, tạo thư mục `vendor/`)

```powershell
docker run --rm -v "${PWD}:/app" -w /app composer:2 install --no-interaction --ignore-platform-reqs
```

**Bước 3. Build giao diện**

Lệnh build của OSPOS gọi `composer` trên máy bạn. Vì máy chưa cài Composer nên tạo một lệnh `composer` tạm chạy qua Docker (chỉ có tác dụng trong cửa sổ PowerShell hiện tại):

```powershell
New-Item -ItemType Directory -Force "$env:TEMP\shim" | Out-Null
Set-Content "$env:TEMP\shim\composer.cmd" '@echo off','docker run --rm -v "%CD%:/app" -w /app composer:2 %*'
$env:Path = "$env:TEMP\shim;$env:Path"

npm install
npm run build
```

**Bước 4. Chạy ứng dụng**

Compose dev dùng hai biến `USERID` và `GROUPID` (vốn của Linux). Trên Windows đặt tạm bằng 1000:

```powershell
$env:USERID = 1000
$env:GROUPID = 1000
docker compose -f docker-compose.dev.yml -f docker-compose.override.yml up --build -d
```

Lần đầu build khoảng 5 đến 10 phút.

**Bước 5. Tạo cấu trúc CSDL**

```powershell
docker exec ospos_dev php spark migrate --all
```

Lệnh này tạo 39 bảng và tài khoản `admin`.

**Bước 6. Mở http://localhost:8080**

| Mục | Giá trị |
| --- | --- |
| Username | `admin` |
| Password | `pointofsale` |

Đây là tài khoản mặc định. Chỉ dùng trên máy mình. Nếu đưa lên mạng thì phải đổi mật khẩu.

### 3.3. Dùng hằng ngày

Mọi lệnh chạy ở thư mục dự án, sau khi đặt `USERID` và `GROUPID` như bước 4.

| Việc | Lệnh |
| --- | --- |
| Bật | `docker compose -f docker-compose.dev.yml -f docker-compose.override.yml up -d` |
| Tắt | `docker compose -f docker-compose.dev.yml -f docker-compose.override.yml down` |
| Xem log ứng dụng | `docker logs --tail 50 ospos_dev` |
| Xem log lỗi PHP | `docker exec ospos_dev sh -c "tail -20 /app/writable/logs/*.log"` |

Bản dev gắn thư mục dự án vào container, nên sửa code là thấy ngay khi tải lại trang, không cần build lại.

**Xóa sạch dữ liệu để làm lại từ đầu:** thêm `-v` vào lệnh tắt (`down -v`). Lệnh này **xóa toàn bộ CSDL**, sau đó phải chạy lại bước 5.

## 4. Docker, cổng 80 và ALLOWED_HOSTNAMES

### 4.1. Hai container

| Container | Vai trò |
| --- | --- |
| `ospos_dev` | PHP 8.2 và Apache, chạy mã nguồn OSPOS từ thư mục dự án |
| `mysql` | MariaDB lưu dữ liệu, không mở cổng ra ngoài (chỉ `ospos_dev` kết nối được) |

Thông tin CSDL (đặt trong `docker-compose.dev.yml`): database `ospos`, user `admin`, password `pointofsale`, root password `pointofsale`.

### 4.2. Vì sao dùng cổng 8080

File `docker-compose.dev.yml` publish cổng `80:80`. Trên nhiều máy Windows cổng 80 đã bị tiến trình hệ thống chiếm (thường là dịch vụ HTTP của IIS), nên container không dùng được cổng này. File [docker-compose.override.yml](docker-compose.override.yml) đổi sang cổng **8080**.

Nếu cổng 8080 cũng bị chiếm, mở `docker-compose.override.yml` và đổi `8080` thành số khác (ví dụ `8081`), nhớ đổi cả trong `ALLOWED_HOSTNAMES`.

### 4.3. ALLOWED_HOSTNAMES là gì

OSPOS chỉ chấp nhận các tên miền nằm trong danh sách trắng, để chống tấn công giả mạo tên miền (Host header injection). Danh sách nằm trong biến môi trường `ALLOWED_HOSTNAMES`.

- Compose dev mặc định chỉ có `localhost`.
- Khi truy cập qua `localhost:8080`, OSPOS thấy tên miền không khớp, ghi cảnh báo `Rejected HTTP_HOST "localhost:8080"` và chuyển hướng nhầm về `http://localhost/login` (cổng 80).
- File override đặt `ALLOWED_HOSTNAMES=localhost,localhost:8080` nên không còn lỗi này.

Ghi chú: docker compose chỉ tự đọc `docker-compose.override.yml` khi **không** dùng `-f`. Các lệnh trong tài liệu này luôn ghi rõ `-f` cho cả hai file. Nếu chạy bản production `docker-compose.yml` bằng lệnh `docker compose up` thì file override cũng được áp dụng.

## 5. Cấu trúc mã nguồn

OSPOS theo mô hình MVC của CodeIgniter 4.

| Thư mục | Nội dung |
| --- | --- |
| `app/Controllers/` | Controller, mỗi module một file: `Sales.php`, `Items.php`, `Reports.php`, `Config.php`... |
| `app/Models/` | Model làm việc với CSDL: `Sale.php`, `Item.php`, `Dinner_table.php`... |
| `app/Views/` | Giao diện PHP, chia theo module (`sales/`, `items/`, `configs/`, `partial/`...) |
| `app/Libraries/` | Logic dùng chung, ví dụ `Sale_lib.php` (giỏ hàng) |
| `app/Database/Migrations/` | Migration thay đổi cấu trúc CSDL |
| `app/Language/<mã ngôn ngữ>/` | Chuỗi giao diện; tiếng Việt ở `app/Language/vi/` |
| `app/Config/` | Cấu hình ứng dụng |
| `public/` | Thư mục web: ảnh, JS, CSS đã build (`public/resources`) |
| `tests/` | Test PHPUnit |
| `writable/` | Log, cache, session, file tải lên |
| `docs/` | Tài liệu gốc: `INSTALL.md`, `BUILD.md`, `UPGRADE.md` |

### Các file liên quan đến bàn ăn

Đây là điểm bắt đầu cho các chức năng bàn của nhóm (sơ đồ bàn, chuyển bàn, gộp bàn):

- `app/Models/Dinner_table.php`: model bàn
- `app/Controllers/Sales.php`: logic chọn bàn khi bán, chặn bàn đang có khách
- `app/Libraries/Sale_lib.php`: giữ bàn đang chọn trong giỏ hàng
- `app/Views/partial/dinner_tables.php`, `app/Views/sales/register.php`, `app/Views/sales/suspended.php`: giao diện
- `app/Views/configs/table_config.php`: màn hình cấu hình bàn

## 6. Quy ước khi viết code

Theo [AGENTS.md](AGENTS.md):

- PHP theo chuẩn **PSR-12**: biến và hàm `camelCase`, class `PascalCase`, hằng số `UPPER_CASE`.
- Chuỗi giao diện mới phải thêm vào **tất cả** thư mục `app/Language/*/`, theo thứ tự alphabet. Bản dịch chưa có thì để chuỗi rỗng `''` (không chép tiếng Anh vào file ngôn ngữ khác).
- Thay đổi CSDL qua migration trong `app/Database/Migrations/`.
- JavaScript dùng `const` hoặc `let`, không dùng `var`.
- Chuẩn hóa code bằng `vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.no-header.php`.
- Chống SQL injection bằng truy vấn tham số hóa, xuất dữ liệu ra giao diện bằng `esc()`.
- Không commit mật khẩu, khóa hay file `.env`.

## 7. Lưu ý khi dùng Git

Sau `npm run build`, Git sẽ báo 3 file bị sửa:

- `app/Views/configs/manage.php`
- `app/Views/login.php`
- `app/Views/partial/header.php`

Đây là thẻ script do build chèn vào, giao diện cần chúng để chạy. **Không commit 3 file này**, và khi `git add` chỉ thêm đúng file mình sửa. Tương tự, không commit các file phiên làm việc `writable/session/ospos_session*`.

## 8. Lỗi thường gặp

| Triệu chứng | Nguyên nhân | Cách xử lý |
| --- | --- | --- |
| `error during connect ... dockerDesktopLinuxEngine` | Docker Desktop chưa bật | Bật Docker Desktop, chờ "Engine running" |
| Cảnh báo `USERID`/`GROUPID` variable is not set, build lỗi ở bước thêm user | Thiếu biến Linux | Đặt `$env:USERID = 1000` và `$env:GROUPID = 1000` |
| Mở trang thấy `Failed opening required ... vendor/codeigniter4/framework/system/Boot.php` | Chưa cài thư viện PHP | Làm bước 2 |
| Trang không có giao diện, thiếu CSS/JS | Chưa build giao diện | Làm bước 3 |
| `npm run build` báo `'composer' is not recognized` | Máy không có lệnh `composer` | Tạo lệnh `composer` tạm như bước 3 |
| `Table 'ospos.ospos_employees' doesn't exist` hoặc không đăng nhập được | CSDL chưa có bảng | Làm bước 5 |
| `port is already allocated` hoặc không mở được trang | Cổng bị chiếm | Đổi cổng trong `docker-compose.override.yml` (mục 4.2) |
| Đăng nhập xong bị chuyển về `http://localhost/login` | Tên miền chưa có trong `ALLOWED_HOSTNAMES` | Kiểm tra file override (mục 4.3) và chạy lại bằng đủ hai file compose |
| Ảnh món không hiện hoặc lỗi khi lưu món | Sai quyền thư mục `writable` | Kiểm tra `writable/` và `public/uploads/` có quyền ghi |

## 9. Tài liệu tham khảo

- [README.md](README.md): README gốc của OSPOS
- [docs/INSTALL.md](docs/INSTALL.md), [docs/BUILD.md](docs/BUILD.md), [docs/UPGRADE.md](docs/UPGRADE.md): cài đặt, build, nâng cấp
- [AGENTS.md](AGENTS.md): quy ước code của dự án
- [tiendo.html](tiendo.html): tiến độ và kế hoạch của nhóm
- Demo: https://demo.opensourcepos.org/ (`admin` / `pointofsale`)
