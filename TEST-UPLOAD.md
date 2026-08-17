# Testing Checklist - Photo Upload Fix

## ✅ Version yang Diuji
Commit: 83e9ca8
File: resources/views/laporan/perawatan.blade.php

## 🧪 Test Plan

### Test 1: Klik Area Upload
**Langkah:**
1. Login ke aplikasi
2. Navigasi ke Laporan Perawatan
3. Klik SALAH SATU area upload (bukan label, bukan icon)

**Expected Result:**
- [ ] File dialog system terbuka
- [ ] Hanya file gambar yang ditampilkan
- [ ] Bisa memilih PNG, JPG, atau WEBP

**Actual Result:**
- [ ] WORKING ✓
- [ ] FAILED ✗

---

### Test 2: Preview Muncul
**Langkah:**
1. Pilih file gambar setelah upload dialog terbuka
2. Tunggu preview muncul

**Expected Result:**
- [ ] Gambar preview langsung muncul di bawah placeholder
- [ ] Placeholder hilang/dihide
- [ ] Image max-height 160px (h-40)
- [ ] Image rounded dengan shadow

**Actual Result:**
- [ ] WORKING ✓
- [ ] FAILED ✗

---

### Test 3: Caption Field
**Langkah:**
1. Setelah foto ter-upload, klik field caption
2. Ketik keterangan foto

**Expected Result:**
- [ ] Caption input visible
- [ ] Placeholder text helpful
- [ ] Alt text image update otomatis saat blur

**Actual Result:**
- [ ] WORKING ✓
- [ ] FAILED ✗

---

### Test 4: Remove Button
**Langkah:**
1. Setelah upload, klik button "Hapus Foto"
2. Verifikasi reset

**Expected Result:**
- [ ] Gambar preview hilang
- [ ] Placeholder muncul kembali
- [ ] Input file value clear
- [ ] Caption clear
- [ ] Remove button hide

**Actual Result:**
- [ ] WORKING ✓
- [ ] FAILED ✗

---

### Test 5: Validation
**Test 5A: Non-image file**
- Upload PDF file
- Expected: Alert "❌ File harus berupa gambar PNG, JPG, atau WEBP!"

**Test 5B: Large file**
- Upload file > 5MB
- Expected: Alert dengan ukuran file detected

**Actual Result:**
- [ ] TYPE validation WORKING ✓
- [ ] SIZE validation WORKING ✓

---

### Test 6: Section Toggle
**Langkah:**
1. Fill section 1 completely
2. Click button "+ Tampilkan Bagian 2"

**Expected Result:**
- [ ] Section 2 smooth scroll into view
- [ ] Semua upload di section 2 juga clickable
- [ ] All 8 upload areas functional

**Actual Result:**
- [ ] WORKING ✓
- [ ] FAILED ✗

---

## 🔍 Console Check

Open Browser DevTools Console (F12):

**Expected Logs:**
```
📁 File selected: my-photo.jpg
✅ Photo uploaded successfully!
✓ Caption updated: Kondisi before cleaning
🗑️ Photo removed: section1_indoor
```

**Console Status:**
- [ ] No errors
- [ ] Info logs present
- [ ] Function calls traced

---

## 🎯 Form Submission Test

1. Fill ALL required fields (Section 1)
2. Upload 4 photos with captions
3. Click "Kirim Laporan"

**Expected:**
- [ ] Form submits successfully
- [ ] Redirect to status page
- [ ] Success message appears
- [ ] Photos saved to database/storage

**Status:**
- [ ] WORKING ✓
- [ ] PENDING TEST ⏳
- [ ] FAILED ✗

---

## 💡 Quick Reference

**Upload Areas per Section:** 4
- Pencucian AC Indoor *
- Pencucian AC Outdoor *
- Kartu Perawatan *
- Lampiran Tambahan (optional)

**Total Upload Areas:** 8 (2 sections)

**Accepted Formats:** PNG, JPG, JPEG, WEBP
**Max Size:** 5MB per file
**Required Fields:** 
- Section 1: Indoor, Outdoor, Kartu (mandatory)
- Section 2: Conditional (required_with="asset_id_2")

---

## 🚀 How to Report Issues

Jika ada masalah saat test, laporkan dengan format:
```
Test #X: [NAME]
Issue: [description of problem]
Browser: [Chrome/Firefox/Safari/Edge]
OS: [Windows/Mac/Linux/iOS/Android]
Screenshot: [attach if possible]
Console Errors: [copy from dev tools]
```
