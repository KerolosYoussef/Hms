<?php
    ob_start();
    session_start();
    include "init.php";
    if(isset($_SESSION['Admin'])){
        include "../Class/Admin.php";
        include "../Class/Specialization.php";
        include "../Class/Doctor.php";    
        $page = isset($_GET['page']) ? $_GET['page'] : "manage";
        if($page == "manage"){
?>
            <div class='userPage'>
                <h1>ADMIN | <?php echo strtoupper($helper->getPageName()); ?></h1>
                <div class='DashboardBox'>
                <p style="margin:5px 20px;font-size:20px">Manage <span style='font-weight:bold'>Doctors</span></p>
                    <div class='container Appointment'>
                        <div class='table-responsive'>
                        <table class='table table-hover'>
                            <thead>
                                <tr>
                                    <th scope='col'>#</th>
                                    <th scope='col'>Specialization</th>
                                    <th scope='col'>Doctor Name</th>
                                    <th scope='col'>Creation Date</th>
                                    <th scope='col'>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($admin->getAllDoctorsData() as $data){
                                        echo "<tr>";
                                            echo "<td>".$data['ID']."</td>";
                                            echo "<td>".$data['Specialization']."</td>";
                                            echo "<td>".$data['Name']."</td>";
                                            echo "<td>".$data['RegisterDate']."</td>";
                                            echo "<td>";
                                                    echo "<a href='?page=edit&id=".$data['ID']."'><i style='color:#0095FF;font-size:15px' class='color fas fa-pencil-alt'></i></a>";
                                                echo "<a onclick='return confirm(\"Are You Sure you want to Delete this Doctor?\")' href='?page=cancel&id=".$data['ID']."' style='margin-left:7px'><i style='color:#0095FF;font-size:19px' class='color fas fa-times'></i></a>";
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <?php 
        } elseif($page == 'edit'){
            $id = isset($_GET['id']) ? $_GET['id'] : -1;
            $dname = $doc->getAllData($id)['Name'];
            if($_SERVER["REQUEST_METHOD"]=="POST"){
                // Validating The Data
                if(empty($_POST['dname'])){
                    $userMsg = "Doctor Name Mustn't Be Empty";
                    $formError[]= 'dname';
                }
                if(empty($_POST['address'])){
                    $addressMsg = "Address Mustn't Be Empty";
                    $formError[]= 'Address';
                }
                if(empty($_POST['email'])){
                    $emailMsg = "Email Mustn't Be Empty";
                    $formError[]= 'Email';
                }
                if(empty($_POST['fees'])){
                    $emailMsg = "Doctor's Fees Mustn't Be Empty";
                    $formError[]= 'fees';
                }
                if(empty($_POST['num'])){
                    $emailMsg = "Contact Number Mustn't Be Empty";
                    $formError[]= 'num';
                }
                if(empty($formError)){
                    // Filtering The Data
                    $docName    = filter_var($_POST['dname'],FILTER_SANITIZE_STRING);
                    $address    = filter_var($_POST['address'],FILTER_SANITIZE_STRING);
                    $specId     = $_POST['spec'];
                    $email      = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
                    $fees       = filter_var($_POST['fees'],FILTER_SANITIZE_EMAIL);
                    $num        = filter_var($_POST['num'],FILTER_SANITIZE_EMAIL);
                    if($doc->updateProfile($specId,$docName,$address,$fees,$num,$email,$dname)){
                        echo "<script>alert('Doctor Updated Successfully')</script>";
                        $helper->redirect("back");
                    }
                }
            }
            ?>
            <div class='userPage'>
            <h1>DOCTOR | <?php echo strtoupper($helper->getPageName()); ?></h1>
            <div class='DashboardBox'>
                <div class='container'>
                    <div class='EditBox'>
                        <h5>Edit Profile</h5>
                        <p class='user'><?php echo $dname ?>'s Profile</p>
                        <p style='color:#333;font-weight:bold'>Profile Reg. Date: <?php echo $doc->getData($dname)['RegisterDate']; ?></p>
                        <hr>
                        <form action='<?php echo $_SERVER['PHP_SELF']; ?>?page=edit&id=<?php echo $id; ?>' method='POST'>
                        <?php if(isset($success)) echo "<span style='color:green;font-weight:bold;font-size:17px'>Profile Updated Successfully</span>"; ?>
                        <div class='form-group'>
                            <label for="#spec">Doctor Specialization</label>
                            <select class='form-control' name="spec" id="spec">
                                <?php
                                    foreach($Specialization->getallData() as $data){
                                        ?> <option <?php echo $doc->getData($dname)['SpecializationID']==$data['ID']?"selected": ' '; ?> value='<?php echo $data["ID"]; ?>'><?php echo $data['Specialization']; ?></option>
                                    <?php }
                                ?>
                            </select>
                            <?php if(isset($userMsg)) echo "<span style='color:red;'>".$userMsg."</span>"; ?>
                        </div>
                        <div class='form-group'>
                            <label for="#dname">Doctor Name</label>
                            <input type="text" name='dname' id='dname' class='form-control' value='<?php echo $doc->getData($dname)['Name']; ?>'>
                            <?php if(isset($userMsg)) echo "<span style='color:red;'>".$userMsg."</span>"; ?>
                        </div>
                        <div class='form-group'>
                            <label for="#Address">Doctor Clinic Address</label>
                            <textarea name='address' id='Address' class='form-control'><?php echo $doc->getData($dname)['Address']; ?></textarea>
                            <?php if(isset($addressMsg)) echo "<span style='color:red;'>".$addressMsg."</span>"; ?>
                        </div>
                        <div class='form-group'>
                            <label for="#fees">Doctor Consultancy Fees</label>
                            <input type="text" name='fees' id='fees' class='form-control' value='<?php echo $doc->getData($dname)['DocFees']; ?>'>
                            <?php if(isset($emailMsg)) echo "<span style='color:red;'>".$emailMsg."</span>"; ?>
                        </div>
                        <div class='form-group'>
                            <label for="#num">Doctor Contact Number</label>
                            <input type="text" name='num' id='num' class='form-control' value='<?php echo $doc->getData($dname)['ContactNo']; ?>'>
                            <?php if(isset($emailMsg)) echo "<span style='color:red;'>".$emailMsg."</span>"; ?>
                        </div>
                        <div class='form-group'>
                            <label for="#email">Doctor Email</label>
                            <input type="email" name='email' id='email' class='form-control' value='<?php echo $doc->getData($dname)['Email']; ?>'>
                            <?php if(isset($emailMsg)) echo "<span style='color:red;'>".$emailMsg."</span>"; ?>
                        </div>
                        <input type='submit' value='Update' class="btn blue-gradient">
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
            <?php 
        } elseif($page == 'cancel'){
            $docID = isset($_GET['id']) ? $_GET['id'] : -1;
            if($doc->deleteDoctor($docID)){
                echo "<script>alert('Doctor Deleted Successfully')</script>";
                $helper->redirect("back");
            }
        }
    ?>
<!-- Footer -->
<footer class="page-footer font-small blue">
  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© 2020 Copyright:
    <a href="https://www.facebook.com/Kirolos.Yossef23" target='_blank'>SlowArrow</a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->
</div>
<?php
    } else {
        $helper->redirect("http://127.0.0.1/Hospital%20Mangement%20System/");
    }
    include $tpl . "footer.php"; 
    ob_end_flush();
    ?>