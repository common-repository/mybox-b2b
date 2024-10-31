<?php
//require files
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';

$xa = (object) [
	"xa" => sanitize_text_field($_GET["xa"]),
	"ordering_number" => sanitize_text_field($_GET["ordering_number"]),
	"first_name" => sanitize_text_field($_GET["first_name"]),
	"last_name" => sanitize_text_field($_GET["last_name"]),
	"address" => sanitize_text_field($_GET["address"]),
	"ref_address" => sanitize_text_field($_GET["reference_of_address"]),
	"phone" => sanitize_text_field($_GET["phone_number_1"]),
	"created_at" => sanitize_text_field($_GET["time"]),

	"b2b" => sanitize_text_field(@$_GET["b2b"]),
	"route" => sanitize_text_field(@$_GET["route"]),
	"area" => sanitize_text_field(@$_GET["area"]),
	"zone" => sanitize_text_field(@$_GET["zone"])
];

$client = $xa->first_name . " " . $xa->last_name;
$ordering_number = $xa->ordering_number;
$address = strlen($xa->address) > 0 ? $xa->address : "N/A";
$ref_address = strlen($xa->ref_address) > 0 ? $xa->ref_address : "N/A";
$phone = strlen($xa->phone) > 0 ? $xa->phone : "N/A";
$created_at = strlen($xa->created_at) > 0 ? date_format(date_create($xa->created_at),"M d, Y") : "N/A";

$zone = strlen($xa->zone) > 0 ? $xa->zone : "N/A";
$route = strlen($xa->route) > 0 ? $xa->route : "N/A";
$area = strlen($xa->area) > 0 ? $xa->area : "N/A";
$b2b = strlen($xa->b2b) > 0 ? $xa->b2b : "N/A";
?>
<!DOCTYPE html>
<html>
   <head>
      <title>Mybox Print</title>
      <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700&display=swap" rel="stylesheet" />
      <link href="http://localhost:8888/wp-content/plugins/mybox-b2b/admin/css/pdf-style.css" rel="stylesheet">
	</head>
	<body>
	<div class="d-flex h-100 w-100 overflow-hidden flex-column flex-sm-row" id="app">
   		<div class="d-flex h-100 w-100 flex-column">
   			<div class="h-100 w-100 z-index-0 overflow-auto">
   				<div class="print-label overflow-hidden d-flex justify-content-center align-items-center w-100 h-100 z-index-0">
			      <div class="page overflow-hidden">
			         <div class="row mx-0">
			            <div class="col-12 d-flex justify-content-between pb-3 px-0">
			               <img src="<?php echo plugin_dir_url(__FILE__); ?>logo-mybox-bw.png" alt="image" class="logo" />
			               <div class="d-flex flex-column text-right my-auto">
			                  <p class="p4 mb-0 text-nowrap">+(507) 831-0522/0523</p>
			                  <p class="p4 mb-0 text-nowrap">delivery@mybox.com.pa</p>
			                  <p class="p4 mb-0 text-nowrap">www.mybox.com.pa</p>
			               </div>
			            </div>
			         </div>
			         <div class="b2b-label">
			            <div class="row mx-0">
			               <div class="separate-line w-100"></div>
			               <div class="col-6 d-flex px-0 flex-column justify-content-center pt-3">
			                  <p class="p4 mb-0">Name</p>
			                  <p class="p1 mb-0 font-weight-bold text-auto-width"><?php echo $client; ?></p>
			               </div>
			               <div class="col-6 d-flex px-0 pr-2 flex-column justify-content-center pt-3 text-right">
			                  <p class="p4 mb-0 text-left pr-3">Phone #</p>
			                  <p class="p1 mb-0 font-weight-bold text-auto-width text-left"><?php echo $phone; ?></p>
			               </div>
			               <div class="col-12 d-flex px-0 py-2 flex-column border-black max-height-110px h-100 overflow-hidden">
			                  <p class="p4 mb-1">Address</p>
			                  <p class="p2 text-break font-weight-bold mb-0 overflow-hidden"><?php echo $address; ?></p>
			               </div>
			               <div class="col-6 d-flex flex-column px-0">
			                  <p class="p4 mb-1">Order #</p>
			                  <p class="p2 mb-0 font-weight-bold text-auto-width"><?php echo $ordering_number; ?></p>
			               </div>
			               <div class="col-6 d-flex flex-column px-0 text-left">
			                  <p class="p4 mb-1">B2B</p>
			                  <p class="p2 mb-0 font-weight-bold text-auto-width"><?php echo $b2b; ?></p>
			               </div>
			               <div class="col-3 d-flex px-0 py-2 flex-column border-black">
			                  <p class="p4 mb-1">Length</p>
			                  <p class="p2 mb-0 text-break text-auto-width font-weight-bold">N/A</p>
			               </div>
			               <div class="col-3 d-flex px-0 py-2 flex-column border-black">
			                  <p class="p4 mb-1">Width</p>
			                  <p class="p2 mb-0 text-break text-auto-width font-weight-bold">N/A</p>
			               </div>
			               <div class="col-3 d-flex px-0 py-2 flex-column border-black">
			                  <p class="p4 mb-1">Height</p>
			                  <p class="p2 mb-0 text-break text-auto-width font-weight-bold">N/A</p>
			               </div>
			               <div class="d-flex col-1">
			                  <div class="border-left border-black h-25 my-auto"></div>
			               </div>
			               <div class="col d-flex px-0 text-right py-2 flex-column border-black">
			                  <p class="p4 mb-1">Weight</p>
			                  <p class="p2 mb-0 text-break text-auto-width font-weight-bold">N/A</p>
			               </div>
			               <div class="col-6 d-flex flex-column px-0 pb-2">
			                  <p class="p4 mb-1">Area</p>
			                  <p class="p2 mb-1 font-weight-bold text-auto-width"><?php echo $area; ?></p>
			               </div>
			               <div class="col col-sm-6 d-flex flex-column px-0 text-left pb-2 pt-1">
			                  <p class="p4 mb-1">Created date</p>
			                  <p class="p2 mb-0 font-weight-bold text-auto-width">
			                     <?php echo $created_at; ?>
			                  </p>
			               </div>
			               <div class="d-flex w-100">
			                  <section class="q-r-code w-100">
			                     <div class="d-flex">
			                        <h1 class="mb-0 size-48px my-auto">XA</h1>
			                        <div class="d-flex align-items-center justify-content-center w-100">
			                           <div class="bg-black d-flex align-items-center justify-content-center height-64px type-holder">
			                              <h2 class="mb-0 text-white ml-3 mr-2">Land</h2>
			                              <i class="fas fa-truck text-white pt-1 mr-2" aria-hidden="true"></i> <!----> <!----> <!----> <!---->
			                           </div>
			                           <div class="arrow-right"></div>
			                        </div>
			                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="height: 100px; width: 100px;background: lightgray">
			                        	<img src="<?php echo plugin_dir_url(__FILE__);?>logo-1.svg" style="width: 55px">
			                        </div>
			                     </div>
			                  </section>
			               </div>
			               <div class="col-12 px-0 d-flex justify-content-center flex-column">
			                  <section class="barcode">
			                     <div class="d-flex flex-column position-relative justify-content-center mt-3 mb-1 overflow-hidden">
			                        <div class="mx-auto position-relative z-index-0">
			                           <div class="brc-inner-holder">
			                              <img src="<?php echo plugin_dir_url(__FILE__) . '/barcode.php?text=' . $xa->xa; ?>" 
			                                 style="margin: auto; width: 295px; height: 70px;" />
			                           </div>
			                        </div>
			                        <div class="d-flex" style="margin-top: -23px">
			                           <h3 class="text-black m-auto px-2 z-index-1 bg-white w-auto">*XA<?php echo $xa->xa;?>*</h3>
			                        </div>
			                     </div>
			                  </section>
			               </div>
			            </div>
			         </div>
			      </div>
 				</div>
   			</div>
   		</div>
	</div>
	<script>
		setTimeout(() => {
			print();
		}, 1000);
	</script>
   </body>
</html>