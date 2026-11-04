# Hướng Dẫn Chạy Website Thành App iOS & Mở Giả Lập Như App Store

Website THEKIEN Fashion đã được tối ưu hóa toàn diện giao diện Mobile, chuẩn PWA và chuẩn iOS Native. Dưới đây là 3 cách chạy giả lập và cài đặt như app tải từ App Store:

---

### Cách 1: Cài trực tiếp lên iPhone / iPad như App tải từ App Store (Dễ nhất & Chuẩn nhất)
Website đã tích hợp sẵn **Apple Web App Manifest & Service Worker**:
1. Dùng trình duyệt **Safari** trên iPhone/iPad truy cập vào website của bạn.
2. Bấm vào nút **Chia sẻ (Share)** ở thanh công cụ dưới đáy Safari (biểu tượng hình vuông có mũi tên chỉ lên).
3. Cuộn xuống và chọn **"Thêm vào MH chính" (Add to Home Screen)**.
4. Đặt tên app là **THEKIEN** hoặc **TEELAB** -> Bấm **Thêm (Add)**.
👉 **Kết quả:**
- Một icon App chuẩn xuất hiện ngay trên màn hình chính của iPhone giống hệt các app cài từ App Store.
- Khi bấm mở, app sẽ chạy **toàn màn hình (Standalone Fullscreen)**, ẩn hoàn toàn thanh địa chỉ duyệt web Safari, có thanh trạng thái iOS riêng, chạy mượt mà độc lập.

---

### Cách 2: Chạy giả lập iPhone / iOS ngay trên máy tính (Appetize.io / Trình duyệt)
Nếu bạn đang dùng Windows và muốn mở một chiếc iPhone ảo để thao tác test app như trên điện thoại:
1. Mở trình duyệt Chrome / Edge trên máy tính.
2. Bấm phím **F12** (hoặc chuột phải -> **Inspect**).
3. Bấm tổ hợp phím **Ctrl + Shift + M** (biểu tượng Toggle Device Toolbar).
4. Ở thanh trên cùng, chọn thiết bị: **iPhone 14 Pro Max** hoặc **iPhone 12 Pro**, chọn tỷ lệ **100%**.
5. Bạn có thể test mượt mà toàn bộ tính năng giỏ hàng, đặt hàng, vuốt chạm như trên máy thật.
6. Hoặc dùng dịch vụ giả lập iOS online trực quan như [Appetize.io](https://appetize.io/demo) để chạy giả lập máy ảo iOS đầy đủ.

---

### Cách 3: Đóng gói thành Native App iOS (.ipa) bằng Capacitor / Xcode (Để đưa lên App Store / TestFlight)
Trong thư mục `ios-app/` đã chuẩn bị sẵn cấu hình:
1. Cài đặt các thư viện:
   ```bash
   cd ios-app
   npm install
   npx cap add ios
   ```
2. Mở dự án trong Xcode (trên máy Mac hoặc qua dịch vụ Mac Cloud):
   ```bash
   npx cap open ios
   ```
3. Trong Xcode, chọn thiết bị giả lập (ví dụ: **iPhone 16 Pro**) rồi nhấn nút **Play (Run)**.
👉 Trình giả lập **iOS Simulator** của Apple sẽ tự động khởi động, cài đặt icon app vào màn hình và mở ứng dụng.
4. Để xuất file nộp lên **App Store**: Trong Xcode chọn **Product -> Archive** rồi tải lên App Store Connect / TestFlight.

