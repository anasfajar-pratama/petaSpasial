Layout Utama

Halaman menggunakan layout Full Screen WebGIS.
┌──────────────────────────────────────────────────────────────┐
│ Top Navigation                                                │
├──────┬───────────────────────────────────────────────┬────────┤
│      │                                               │Toolbar │
│Sidebar                                               │Search  │
│Layer                                                 │        │
│                                                      │        │
│                                                      │        │
│                Leaflet / Map Canvas                  │        │
│                                                      │        │
│                                                      │ Zoom   │
│                                                      │ Coord  │
└──────────────────────────────────────────────────────────────┘

Map menjadi base layer yang memenuhi seluruh viewport (100vw x 100vh).

Semua komponen UI berada di atas map menggunakan position:absolute.

1. Top Navigation
Posisi
top: 20px
left: 20px
right: 20px

Visual
Floating Navigation
Background putih
Border Radius besar (±40px)
Shadow ringan
Tinggi sekitar 80–90px

Layout menggunakan
display:flex;
justify-content:space-between;
align-items:center;

struktur
+-------------------------------------------------------------+
| Logo |          Menu Navigation             |        Menu    |
+-------------------------------------------------------------+

Menu berada di tengah.

Logo berada di kiri.

Tidak menggunakan sidebar permanen.

2. Floating Sidebar Trigger

Posisi
top: 145px
left: 30px

Visual

Square button
Width ±60px
Height ±60px
Background biru tua
Radius 12px
Icon putih

Berfungsi membuka panel layer.
3. Layer Panel

Panel muncul sebagai Floating Card.

position:absolute;
left:30px;
top:220px;

Ukuran kira-kira

width : 400px
height: 620px

Visual
┌─────────────────────────────┐
│ Katalog Layer      ▼        │
├─────────────────────────────┤
│ ☑ Layer 1            ⋯      │
│ ☑ Layer 2            ⋯      │
│ ☑ Layer 3            ⋯      │
│                             │
│                             │
├─────────────────────────────┤
│     Hapus Semua             │
└─────────────────────────────┘

Style

Background putih
Radius 18px
Shadow medium
Padding besar (24px)
Header Panel

Menggunakan button berwarna biru.

width:100%
height:56px

Border radius sekitar 12px.

List Layer

Scrollable.

Setiap item terdiri dari

Checkbox

Nama Layer

Menu (...)

Layout

display:flex;
justify-content:space-between;
align-items:flex-start;

Nama layer dapat multiline.

Scrollbar

Scrollbar custom.

Lebar sekitar

8px

Thumb berwarna biru.

Footer

Button merah penuh.

width:100%
height:56px

Border radius

12px

4. Map Toolbar

Toolbar berada di kanan atas.

position:absolute;
top:145px;
right:20px;

Bentuk horizontal.

┌─┬─┬─┬─┬─┬─┬─┬─┐
│ │ │ │ │ │ │ │ │
└─┴─┴─┴─┴─┴─┴─┴─┘

Masing-masing berupa

Floating Icon Button.

Style

44x44

background:white

border-radius:10px

box-shadow

Gap antar icon sekitar

8px
5. Search Box

Berada tepat di bawah toolbar.

width:380px
height:50px

Visual

┌──────────────────────────────┐
│ Cari alamat....              │
└──────────────────────────────┘

Style

Background putih
Radius 12px
Shadow tipis
Placeholder abu-abu
6. Zoom Control

Berada kanan bawah.

Floating.

+
-

Style mengikuti Leaflet tetapi telah dikustomisasi.

Background putih
Radius 10px
Shadow
7. Coordinate Widget

Posisi

bottom:20px
right:20px

Visual

┌──────────────────────────┐
│ Latitude   Longitude     │
│ -6.xxxxxx 106.xxxxxx     │
└──────────────────────────┘

Card putih.

Radius besar.

Padding sekitar

20px
8. Basemap Button

Posisi

right:20px
bottom:170px

Floating icon.

Background putih.

Radius 10px.

9. Bottom Drawer Handle

Bagian bawah tengah.

┌────────────┐
│      ▲     │
└────────────┘

Kemungkinan digunakan untuk membuka Bottom Sheet.

Design System
Warna

Primary

#0C3F8A

Secondary

#FF8A00

Danger

#FF2020

Background

#FFFFFF

Border

#EAEAEA

Text

#1E1E1E

Placeholder

#A5A5A5
Border Radius
Component	Radius
Navbar	40px
Card	18px
Button	12px
Toolbar Button	10px
Search	12px
Shadow

Semua komponen menggunakan shadow ringan, misalnya:

box-shadow:
0 4px 15px rgba(0,0,0,.15);

Tidak ada border yang dominan.

Frontend Architecture (Komponen)

Tim frontend dapat memecah UI menjadi komponen seperti berikut:

<AppLayout>
 ├── TopNavbar
 ├── FloatingMenuButton
 ├── LayerSidebar
 │    ├── LayerHeader
 │    ├── LayerList
 │    ├── LayerItem
 │    └── LayerFooter
 ├── MapCanvas (Leaflet/OpenLayers)
 ├── MapToolbar
 │    ├── HomeButton
 │    ├── FullscreenButton
 │    ├── UploadButton
 │    ├── FitExtentButton
 │    ├── MeasureButton
 │    ├── ScaleButton
 │    ├── LocateButton
 │    └── LanguageSwitcher
 ├── SearchControl
 ├── BasemapControl
 ├── ZoomControl
 ├── CoordinateWidget
 └── BottomDrawerHandle

Struktur komponen seperti ini membuat setiap bagian UI modular, mudah diuji, dan memudahkan pemeliharaan. Selain itu, seluruh elemen overlay (toolbar, panel layer, search, zoom, koordinat) dapat diposisikan menggunakan position: absolute di atas MapCanvas,