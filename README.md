# Cafe POS — Hệ thống quản lý bán hàng cho quán cà phê

<p align="center">
  <strong>Đồ án cuối kỳ — Môn Phần mềm mã nguồn mở | Nhóm 8</strong><br>
  Phát triển và tùy chỉnh trên nền tảng <a href="https://github.com/opensourcepos/opensourcepos">Open Source Point of Sale (OSPOS)</a>
</p>

> **Trạng thái dự án:** Đang phát triển và kiểm thử các chức năng dành riêng cho quán cà phê. Các chức năng chưa được tích hợp, kiểm thử hoặc merge vào nhánh `master` không được xem là đã hoàn thành.

## 1. Giới thiệu

**Cafe POS** là đồ án xây dựng hệ thống quản lý bán hàng cho quán cà phê, tận dụng các thành phần có sẵn của OpenSourcePOS và mở rộng cho nghiệp vụ phục vụ tại bàn. Hệ thống hướng tới việc quản lý thực đơn, bán hàng, thanh toán, in phiếu, quản lý nhân viên, hàng tồn kho và theo dõi doanh thu trong một ứng dụng web thống nhất.

**Mục tiêu:**

- Sử dụng, tìm hiểu và tùy chỉnh một dự án phần mềm mã nguồn mở thực tế.
- Phát triển những tính năng phù hợp quy trình bán hàng quán cà phê.
- Rèn luyện PHP, CodeIgniter 4 (MVC), MySQL/MariaDB, HTML/CSS/JavaScript và Docker.
- Phân công công việc, phát triển song song trên GitHub, kiểm thử và trình bày sản phẩm cuối kỳ.

**Repository nhóm:** [HaNguyen051/ospos-nhom8-opensource](https://github.com/HaNguyen051/ospos-nhom8-opensource)
**Mã nguồn gốc:** [opensourcepos/opensourcepos](https://github.com/opensourcepos/opensourcepos)

## 2. Công nghệ sử dụng

| Thành phần | Công nghệ | Vai trò |
| --- | --- | --- |
| Nền tảng | OpenSourcePOS (OSPOS) 3.4.x | Cung cấp các nghiệp vụ POS nền tảng |
| Backend | PHP 8.2+; CodeIgniter 4 | Xử lý nghiệp vụ, Controller/Model/Routes |
| Frontend | HTML, CSS, Bootstrap, JavaScript, jQuery | Giao diện quản lý và bán hàng |
| Cơ sở dữ liệu | MySQL / MariaDB | Lưu sản phẩm, đơn hàng, nhân viên, cấu hình |
| Môi trường chạy thử | Docker Desktop, Docker Compose | Khởi chạy ứng dụng và CSDL |
| Công cụ phát triển | VS Code, Git, GitHub, Composer, Node.js | Viết mã, build, cộng tác và kiểm thử |

> Phiên bản, cổng và cấu hình chính xác phụ thuộc nhánh mã nguồn và file Compose đang được sử dụng. Trước khi cài đặt, nên xem `docs/INSTALL.md` và `docs/BUILD.md`.

## 3. Chức năng hệ thống

### 3.1. Kế thừa từ OpenSourcePOS

- **Hàng hóa (Items):** Quản lý sản phẩm, danh mục, giá bán và tồn kho.
- **Bán hàng (Sales):** Tạo giao dịch, thanh toán và quản lý đơn hàng.
- **Phiếu thanh toán:** Hiển thị và in lại giao dịch.
- **Khách hàng, nhà cung cấp, nhân viên:** Quản lý thông tin và phân quyền.
- **Nhập hàng (Receivings):** Theo dõi hoạt động nhập kho.
- **Báo cáo (Reports):** Các báo cáo bán hàng, kho và giao dịch cơ bản.
- **Cấu hình (Configuration):** Tên cửa hàng, ngôn ngữ, tiền tệ, mẫu phiếu và các thiết lập khác.
- **Bàn (Dinner tables):** Khả năng quản lý bàn ở mức nền tảng, cần kiểm tra cấu hình cụ thể.

### 3.2. Nhiệm vụ phát triển của nhóm

| Thành viên | Module được phân công | Nội dung dự kiến |
| --- | --- | --- |
| 1 — Nhóm trưởng | Chuyển bàn, gộp bàn | Thay đổi bàn phục vụ, gộp đơn/bàn đúng nghiệp vụ |
| 2 | Sơ đồ bàn trực quan | Hiển thị bàn, trạng thái và thao tác trên giao diện |
| 3 | Ghi chú món và chọn size | Ít đá/ít đường, kích cỡ đồ uống và dữ liệu đơn hàng |
| **4** | **Phiếu thanh toán, tiếng Việt, VNĐ** | **Tùy chỉnh mẫu in Cafe POS, bản dịch và định dạng tiền tệ** |
| 5 | Báo cáo doanh thu, món bán chạy | Thống kê theo ngày và sản phẩm bán chạy |

**Chú ý về tiến độ:** Bảng trên là **phân công**, không phải xác nhận hoàn thành. Module 4 có bản vá và bản xem trước được thử nghiệm trên máy cá nhân; cần kiểm thử tích hợp và tạo Pull Request trước khi xem là đã có trên repository chung.

## 4. Cấu trúc mã nguồn

```text
ospos-nhom8-opensource/
├── app/
│   ├── Config/              # Cấu hình ứng dụng
│   ├── Controllers/         # Tiếp nhận yêu cầu HTTP và điều phối nghiệp vụ
│   ├── Models/              # Truy vấn và thao tác dữ liệu
│   ├── Views/               # Giao diện PHP, bao gồm Sales / Receipt
│   ├── Language/vi/         # Chuỗi ngôn ngữ tiếng Việt
│   ├── Libraries/           # Logic dùng chung như Sale_lib.php
│   └── Database/Migrations/ # Thay đổi cấu trúc cơ sở dữ liệu
├── public/                  # Tệp phục vụ web, CSS, JS, hình ảnh
├── docker/                  # Cấu hình Docker/MariaDB
├── docs/                    # Tài liệu INSTALL, BUILD, UPGRADE
├── tests/                   # Các bài kiểm thử
├── writable/                # Log, cache, dữ liệu runtime
├── docker-compose.yml       # Chạy ứng dụng từ Docker image
├── docker-compose.dev.yml   # Chạy mã nguồn để phát triển
├── docker-compose.override.yml  # Ghi đè cấu hình theo môi trường
├── .env.example             # Mẫu cấu hình môi trường
└── README.md
```

**Luồng xử lý MVC (khái quát):** Trình duyệt → Routes/Controller → Model/Library → MySQL/MariaDB → View → Trình duyệt.

## 5. Chuẩn bị môi trường Windows

Cài đặt:

1. [Docker Desktop](https://www.docker.com/products/docker-desktop/) và bật WSL2 khi được yêu cầu.
2. [Git](https://git-scm.com/downloads).
3. [Visual Studio Code](https://code.visualstudio.com/).
4. [Node.js](https://nodejs.org/) 22+ nếu cần build từ mã nguồn.

Mở **PowerShell** để kiểm tra:

```powershell
docker --version
docker compose version
git --version
node -v
npm -v
```

Đảm bảo Docker Desktop đang ở trạng thái **Engine running** trước khi chạy các lệnh Compose.

## 6. Chạy nhanh hệ thống OSPOS bằng Docker

> Hướng này dùng image OSPOS đóng gói sẵn để xem và dùng các chức năng nền tảng. **Nó không tự tải các file PHP mà bạn sửa trên máy.** Muốn lập trình module, xem Mục 7.

### 6.1. Tải repository nhóm

```powershell
cd D:\DoAn_Cafe
git clone https://github.com/HaNguyen051/ospos-nhom8-opensource.git
cd ospos-nhom8-opensource
code .
```

Nếu đã clone, bỏ qua `git clone` và mở trực tiếp thư mục đã có.

### 6.2. Tạo cấu hình môi trường

```powershell
if (!(Test-Path .env)) { Copy-Item .env.example .env }
```

Kiểm tra `docker-compose.yml`, `.env`, tài liệu cài đặt của nhánh đang dùng. **Không đăng `.env` lên GitHub** và không chia sẻ các khóa bảo mật.

### 6.3. Khởi động

Repository nhóm có file `docker-compose.override.yml` dùng để chuyển cổng ứng dụng sang **8080**. Vì vậy khi chạy bản nhóm:

```powershell
docker compose -f docker-compose.yml -f docker-compose.override.yml config --quiet
docker compose -f docker-compose.yml -f docker-compose.override.yml up -d
docker compose -f docker-compose.yml -f docker-compose.override.yml ps
```

Truy cập: **http://localhost:8080** (nếu cấu hình cổng hiện tại của nhóm không thay đổi).

Đối với bản Fork cá nhân `D:\DoAn_Cafe\Cafe_POS`, bạn đã từng dùng `http://localhost` với các file override riêng. Hai môi trường này **không nhất thiết có cùng cổng**: luôn xem cột `PORTS` trong `docker compose ps`.

**Tài khoản demo mặc định cho bản cài mới (khi chưa đổi):**

- Tên đăng nhập: `admin`
- Mật khẩu: `pointofsale`

Chỉ dùng tài khoản mẫu cho môi trường thử nghiệm và đổi mật khẩu khi cần.

### 6.4. Dừng hoặc chạy lại

```powershell
# Dừng nhưng giữ cơ sở dữ liệu
docker compose -f docker-compose.yml -f docker-compose.override.yml stop

# Khởi động lại
docker compose -f docker-compose.yml -f docker-compose.override.yml up -d

# Xem log
docker compose -f docker-compose.yml -f docker-compose.override.yml logs --tail=80 ospos
```

**Không dùng `docker compose down -v` nếu muốn giữ dữ liệu:** tham số `-v` sẽ xóa các volume do Compose quản lý, bao gồm dữ liệu CSDL.

## 7. Môi trường lập trình — chạy mã nguồn đã sửa

> Nếu chỉ dùng `docker-compose.yml`, chương trình chạy từ image `jekkos/opensourcepos:master`; sửa file PHP trên ổ D **không tự cập nhật** vào container. Để lập trình đồng thời với các thành viên khác, phải sử dụng môi trường dev có mount thư mục mã nguồn.

Thực hiện trong thư mục clone của **nhóm**, không phải thư mục bản Fork cá nhân.

### 7.1. Cài các thư viện và build giao diện

Theo [hướng dẫn build gốc](docs/BUILD.md), dự án cần Composer, Node.js và npm:

```powershell
composer install
npm install
npm run build
```

Nếu Windows chưa có Composer, có thể tham khảo cách dùng Docker trong [README_vi.md](README_vi.md) của nhóm. Trước khi khởi động môi trường dev, cần tạo `.env` và chuẩn bị đầy đủ `vendor/` cùng tài nguyên frontend.

### 7.2. Chạy Docker dev

Trong PowerShell Windows, các biến USERID/GROUPID có thể cần khai báo để khớp cấu hình Compose dev:

```powershell
$env:USERID = "1000"
$env:GROUPID = "1000"
docker compose -f docker-compose.dev.yml -f docker-compose.override.yml config --quiet
docker compose -f docker-compose.dev.yml -f docker-compose.override.yml up --build -d
```

Nếu đã chạy môi trường Compose ở Mục 6, **dừng môi trường đó trước** để tránh xung đột cổng 8080 hoặc container `mysql`.

Nếu cơ sở dữ liệu mới chưa có bảng, kiểm tra log và migration theo tài liệu của nhánh; trên môi trường dev của nhóm có thể dùng:

```powershell
docker exec ospos_dev php spark migrate --all
```

Sau đó xem trạng thái và mở `http://localhost:8080`. Khi đã mount mã nguồn thành công, những chỉnh sửa PHP/View thường có thể kiểm tra bằng cách tải lại trình duyệt; các thay đổi phụ thuộc frontend build có thể cần chạy `npm run build` lại.

## 8. Module 4 — Phiếu thanh toán Cafe POS

**Phụ trách:** Thành viên 4. **Trạng thái:** Có bản vá chạy thử trên máy cá nhân; chưa mặc định có mặt trên `master` của nhóm.

### 8.1. Phạm vi

- Tạo mẫu phiếu **“Mẫu phiếu thanh toán”**, tên quán **Cafe POS**.
- Hiển thị dữ liệu bán hàng: mã giao dịch, sản phẩm, đơn giá, số lượng, tổng tiền; thông tin bàn, nhân viên và khách hàng khi có.
- Định dạng VNĐ thông qua thiết lập Localization của OSPOS.
- Điều chỉnh một số chuỗi tiếng Việt trong phần giao diện liên quan.
- Tối ưu bố cục in khổ giấy nhiệt 80 mm.
- Hiển thị ghi chú/size khi dữ liệu đã có từ các module khác; Module 4 không tự tạo logic ghi chú hoặc lựa chọn size.

### 8.2. File liên quan

| File | Mục đích |
| --- | --- |
| `app/Views/sales/receipt_cafe.php` | Giao diện phiếu thanh toán Cafe POS |
| `app/Libraries/Sale_lib.php` | Cho phép chọn mẫu phiếu tùy chỉnh |
| `app/Views/configs/receipt_config.php` | Thêm mẫu vào danh sách lựa chọn |
| `app/Language/vi/Common.php` | Một số nhãn chung tiếng Việt |
| `app/Language/vi/Sales.php` | Một số nhãn bán hàng tiếng Việt |
| `tests/smoke_render.php` | Kiểm thử nhanh dựng mẫu với dữ liệu giả lập, nếu đã chép bản vá |

Các file trên là **phạm vi bản vá**, không có nghĩa chúng đều đã được đưa lên nhánh `master`.

### 8.3. Cấu hình trong giao diện

Đăng nhập vào OSPOS rồi vào **Văn phòng → Configuration** (tên có thể là *Cấu hình*):

- **Information:** đặt `Company Name = Cafe POS`, nhập địa chỉ, số điện thoại hợp lệ và logo tối ưu dung lượng.
- **Localization:** chọn `vi_VN`, `VND`, ký hiệu `₫`, 0 chữ số thập phân, ngôn ngữ Vietnamese và múi giờ Việt Nam.
- **Receipt:** chọn **Mẫu phiếu thanh toán**, bật **Show Company Name**; bật mô tả nếu cần hiển thị ghi chú.

Nếu mẫu chưa xuất hiện hoặc việc in bị lỗi, cần xác nhận bản vá đã được cài đúng và file PHP thật sự được mount/build vào container.

### 8.4. Kiểm thử nhanh riêng cho Module 4

Nếu đã có `tests/smoke_render.php` trên máy:

```powershell
docker run --rm -v "${PWD}:/work" -w /work php:8.3-cli php tests/smoke_render.php
```

Kết quả mong đợi: `PASS: receipt_cafe.php renders sample data (PHP isolated smoke test).` Đây chỉ là **kiểm thử đơn lẻ**, không thay thế kiểm thử bán hàng hoàn chỉnh.

### 8.5. Chạy thử Module 4 trên bản cá nhân (chưa đẩy GitHub)

Nếu bạn đã cài bản vá trong `D:\DoAn_Cafe\Cafe_POS` và đang dùng đúng các file Compose từ lần chạy thử trước:

```powershell
cd D:\DoAn_Cafe\Cafe_POS
docker compose -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.module4.yml config --quiet
docker compose -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.module4.yml up -d
docker compose -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.module4.yml ps
```

Mở **http://localhost** hoặc **http://localhost:8080** theo cổng hiển thị ở `docker compose ps`. File `docker-compose.module4.yml` chỉ dành cho môi trường thử nghiệm cục bộ; không mặc định có trong repository nhóm. Không chạy đồng thời hai bản OSPOS nếu chúng chiếm cùng cổng/tên container MySQL.

## 9. Hướng dẫn bán hàng mẫu và kiểm thử tích hợp

Ví dụ tạo sản phẩm **Cà phê sữa**:

| Trường | Dữ liệu thử |
| --- | --- |
| Mã hàng | `CF001` |
| Tên hàng hóa | Cà phê sữa |
| Thể loại | Cà phê |
| Giá bán lẻ | `30000` |
| Số lượng tồn | `100` |
| Thuế | Không áp dụng cho phép thử này |

**Các bước:**

1. Vào **Hàng hóa (Items)** → **Hàng hóa mới** → điền dữ liệu → **Gửi**.
2. Vào **Bán hàng (Sales)** → tìm `CF001` hoặc `Cà phê sữa`.
3. Đặt số lượng `2`; xác nhận tổng trước thuế **60.000 ₫**.
4. Chọn thanh toán bằng tiền mặt và hoàn thành giao dịch.
5. Mở lại phiếu thanh toán và kiểm tra giao diện **Cafe POS**; có thể xem lại qua danh sách đơn hàng/giao dịch trong Sales.

### Bộ test case tối thiểu

| Mã | Tình huống | Kết quả mong đợi |
| --- | --- | --- |
| TC-01 | Hai ly cà phê sữa giá 30.000 ₫/ly, không thuế/giảm giá | Tổng 60.000 ₫ |
| TC-02 | Cấu hình Localization = VND, 0 số lẻ | Tiền hiển thị theo VNĐ, không có phần thập phân |
| TC-03 | Chọn mẫu `receipt_cafe` | Phiếu hiển thị tên Cafe POS và các dòng hàng |
| TC-04 | Mở lại giao dịch đã hoàn tất | Có thể xem/in lại đúng thông tin giao dịch |
| TC-05 | Xem trên khổ in nhiệt 80 mm | Nội dung không tràn khổ in nghiêm trọng |
| TC-06 | Chuyển về mẫu mặc định | Mẫu phiếu gốc vẫn hoạt động |

Chỉ đánh dấu test **Pass** sau khi đã thực hiện trên phiên bản chạy thật; ghi lại ảnh chụp, kết quả thực tế và lỗi nếu có.

## 10. Làm việc nhóm trên GitHub

Tất cả thành viên phát triển từ repository **chung**; không đẩy trực tiếp lên `master`.

**Ví dụ cho Module 4:**

```powershell
cd D:\DoAn_Cafe\ospos-nhom8-opensource
git switch master
git pull --ff-only origin master
git switch -c feature/receipt-vietnamese

# Sau khi chỉnh sửa và kiểm thử
git status
git add app/Views/sales/receipt_cafe.php app/Libraries/Sale_lib.php app/Views/configs/receipt_config.php app/Language/vi/Common.php app/Language/vi/Sales.php
git commit -m "feat(module4): add Cafe POS receipt and Vietnamese labels"
git push -u origin feature/receipt-vietnamese
```

Sau khi push, mở **Pull Request** trên [repository nhóm](https://github.com/HaNguyen051/ospos-nhom8-opensource/pulls) để nhóm trưởng review và merge.

**Quy tắc cộng tác:**

- Chỉ commit những file có thay đổi thực sự và thuộc phần việc của mình.
- Không commit `.env`, mật khẩu, token, khóa bảo mật, log, session hoặc file dữ liệu riêng tư.
- Dùng migration khi thay đổi cấu trúc cơ sở dữ liệu; không sửa CSDL nhóm một cách âm thầm.
- Thống nhất interface/dữ liệu dùng chung giữa module bàn, ghi chú món và hóa đơn.
- Phân biệt commit mã nguồn **kế thừa OSPOS** với commit **đóng góp mới của nhóm**.

## 11. Lỗi thường gặp

| Hiện tượng | Cách kiểm tra / xử lý |
| --- | --- |
| Không mở được localhost | Chạy `docker compose ps`, kiểm tra cột `PORTS`, xem cổng 80 hay 8080 |
| Docker báo `Cannot connect to Docker daemon` | Mở Docker Desktop, chờ Engine running |
| Container `ospos` liên tục `Restarting` | Chạy `docker compose logs --tail=100 ospos` để xác định lỗi |
| `Unable to persist throttle.key to /app/.env` | Lỗi ghi khóa vào `.env` trong container; xem lại quyền ghi và cách mount file; không tự tạo lại khóa trên cơ sở dữ liệu đang sử dụng |
| Đổi PHP nhưng giao diện không đổi | Bạn có thể đang chạy Docker image dựng sẵn, chưa dùng Compose dev hoặc mount bản vá |
| Báo `port is already allocated` | Dừng Compose đang dùng cổng đó hoặc đổi mapping cổng |
| Đăng nhập thất bại do thiếu bảng | Kiểm tra migration và log CSDL của môi trường dev |
| Logo báo `Company logo is too large of a file` | Nén ảnh rồi tải lại trong Configuration → Information |
| Không thấy **Mẫu phiếu thanh toán** | Kiểm tra có `receipt_cafe.php`, script cài hoàn tất và container đang tải đúng code |

Không xóa volume hoặc tạo lại CSDL nếu chưa xác định rõ nguyên nhân.

## 12. Báo cáo và tài liệu bàn giao

Đồ án cuối kỳ dự kiến cần:

- Mã nguồn GitHub và lịch sử commit/Pull Request của từng thành viên.
- Mô tả hệ thống, các tính năng kế thừa và các tính năng tự phát triển.
- Sơ đồ Use Case, ERD và luồng xử lý chính.
- Ảnh chụp giao diện, hướng dẫn sử dụng và thao tác demo.
- Test case, kết quả chạy thử và minh chứng kiểm thử.
- Báo cáo đồ án, slide thuyết trình và phân công người trình bày.

## 13. Bản quyền và ghi công

Cafe POS được phát triển dựa trên **Open Source Point of Sale** của cộng đồng tác giả OSPOS. Mã nguồn gốc áp dụng giấy phép MIT kèm các điều khoản/thông báo cụ thể trong file [LICENSE](LICENSE) và [README gốc](https://github.com/opensourcepos/opensourcepos/blob/master/README.md).

**Giữ nguyên các thông báo bản quyền và phần footer bắt buộc của OSPOS**; không xóa `LICENSE`, không thay footer bản quyền bằng tên nhóm và không tuyên bố nhóm là tác giả của toàn bộ mã nguồn gốc. Phần thay đổi của nhóm phải được mô tả và ghi công riêng.

---

**Nhóm 8 — Đồ án cuối kỳ Phần mềm mã nguồn mở**
**Cafe POS | OpenSourcePOS + PHP + MySQL/MariaDB**
