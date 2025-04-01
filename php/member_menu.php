<?php
// เมนูของสมาชิก
echo '<div class="sidebar" style="position: absolute; top: 70px;">';
echo '    <h4 class="text-center py-3">เมนูของสมาชิก</h4>';
echo '    <ul class="nav flex-column">';

// หน้าหลัก
echo '        <li class="nav-item">';
echo '            <a href="/web_badmintaon_khlong_6/Member/Member home page.php" class="nav-link section-title"><i class="fas fa-home"></i> หน้าหลัก</a>';
echo '        </li>';

// ประวัติการจองสนาม
echo '        <li class="nav-item">';
echo '            <a class="nav-link1 section-title1"><i class="fas fa-history"></i> ประวัติการจองสนาม</a>';
echo '            <ul class="submenu">';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/Booking history.php"><i class="fas fa-file-alt"></i> ประวัติการจองสนามแบบทั่วไป</a></li>';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/Gang history.php"><i class="fas fa-file-alt"></i> ประวัติการจองเข้าเล่นแบบก๊วน</a></li>';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/History open group.php"><i class="fas fa-file-alt"></i> ประวัติการขอเปิดสนามแบบก๊วน</a></li>';
echo '            </ul>';
echo '        </li>';

// การจองสนามแบทั่วไป
echo '        <li class="nav-item">';
echo '            <a class="nav-link1 section-title1"><i class="fas fa-calendar"></i> การจองสนามแบทั่วไป</a>';
echo '            <ul class="submenu">';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/General booking.php"><i class="fas fa-file-alt"></i> จองสนามแบบทั่วไป</a></li>';
echo '            </ul>';
echo '        </li>';

// การจองสนามแบบก๊วน
echo '        <li class="nav-item">';
echo '            <a class="nav-link1 section-title1"><i class="fas fa-users"></i> การจองสนามแบบก๊วน</a>';
echo '            <ul class="submenu">';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/Listgroups.php"><i class="fas fa-file-alt"></i> ขอเข้าเล่นแบบก๊วน</a></li>';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/Open group.php"><i class="fas fa-file-alt"></i> ขอเปิดสนามแบบก๊วน</a></li>';
echo '            </ul>';
echo '        </li>';

// ติดต่อเรา
echo '        <li class="nav-item">';
echo '            <a class="nav-link section-title" href="/web_badmintaon_khlong_6/Member/Member contact us page.php"><i class="fas fa-phone"></i> ติดต่อเรา</a>';
echo '            <ul class="submenu">';
echo '                <li><a href="/web_badmintaon_khlong_6/Member/payment.php"><i class="fas fa-file-alt"></i>วิธีการชำระเงิน</a></li>';
echo '            </ul>';
echo '        </li>';

echo '    </ul>';
echo '</div>';
?>


<style>
    /* เมนูต่างๆ */
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 1420px;
  width: 300px;
  background-color: #79B7FF;
  color: #000000;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  margin-top: 0px;
}
.sidebar h4 {
  color: rgb(0, 0, 0);
  font-weight: bold;
  border-bottom: 2px solid #000000;
  margin-bottom: 15px;
  padding-bottom: 10px;
}

.sidebar .nav-link {
  color: #000;
  padding: 10px 20px;
  text-decoration: none;
  display: flex;
  align-items: center;
  border-radius: 5px;
}


.sidebar .nav-link1 {
  color: #000;
  padding: 10px 20px;
  text-decoration: none;
  display: flex;
  align-items: center;
  border-radius: 5px;
}


.sidebar .nav-link i {
  margin-right: 10px;
}

.sidebar .nav-link:hover {
  background-color: #ffffff;
  color: #000;
}
/* กำหนดสไตล์ให้กับคลาส nav-link12 */
.nav-link12 {
  font-size: 16px;
  font-weight: bold;
  text-decoration: none; /* ลบขีดเส้นใต้ */
  color: #000000; /* สีตัวอักษร */
  margin-left: auto; /* จัดให้ชิดขวา */
  margin-right: 0; /* ป้องกันการเว้นว่างขวา */
  display: block; /* หรือ inline-block เพื่อควบคุมการจัดวาง */
}

/* สไตล์เพิ่มเติมสำหรับคลาส btn */
.nav-link12.btn {
  color: #000000; /* สีข้อความ */
  background-color: #79B7FF;
  border-radius: 5px; /* มุมโค้ง */
  text-align: center; /* จัดข้อความให้อยู่ตรงกลาง */
  width: fit-content; /* ขนาดตามเนื้อหา */
  height: 40px;
  margin-left: auto; /* ชิดขวา */
  margin-right: 0; /* ป้องกันเว้นว่างเกิน */
}

/* สไตล์เมื่อ hover */
.nav-link12.btn:hover {
  background-color: #636363; /* เปลี่ยนสีพื้นหลัง */
  color: #000000; /* เปลี่ยนสีตัวอักษร */
}


.sidebar .section-title {
  font-weight: bold;
  color: #000000;
  background-color: #c7c7c7;
  padding: 10px 20px;
  margin: 5px 0;
}

.sidebar .section-title1 {
  font-weight: bold;
  color: #000000;
  background-color: #8f8f8f;
  padding: 10px 20px;
  margin: 5px 0;
}



.sidebar .submenu {
  list-style: none;
  padding-left: 20px;
  margin: 0;
}

.sidebar .submenu li a {
  color: #000;
  padding: 5px 20px;
  text-decoration: none;
  display: block;
  font-size: 0.9em;
  border-radius: 5px;
}

.sidebar .submenu li a:hover {
  background-color: #ffffff;
}

</style>