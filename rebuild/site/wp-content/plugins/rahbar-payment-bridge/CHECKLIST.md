# Payment safety checklist

- [x] قرارداد Adapter و مدل‌های مبلغ/ارز/نتیجه
- [x] درگاه Sandbox فقط در محیط local/test
- [x] اعتبارسنجی order ID/key، payment method و session token
- [ ] قفل سفارش و claim یکتای transaction — callback تکراری پاس؛ تست هم‌زمان واقعی باقی است
- [x] نمایش در Classic Checkout و Checkout Block
- [x] تست success/decline/mismatch/duplicate و ثبت evidence
- [ ] Adapter واقعی Zibal فقط پس از sandbox و تأیید تنظیمات/secret
- [ ] اتصال Course Access فقط از رویداد verified payment
- [ ] migration/reconciliation سفارش، transaction و entitlement قبل Cutover
