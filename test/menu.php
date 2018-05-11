    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
		  <a class="navbar-brand" href="index.php">Open Position</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <!-- <li class="active"><a href="closePosition.php">Close Position</a></li> -->
            <li class="active"><a href="closePosition.php">Close Position</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Profit / Loss <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                	<li class="dropdown-header">Company</li>
                  	<li><a href="companyPosition.php">Detailed</a></li>
                  	<li><a href="accountfair.php">Accounting & Fair</a></li>
                  	<!-- <li><a href="notional.php">Notional</a></li> -->
                    <li><a href="notional2.php">Notional</a></li>

                </ul>
             </li>
			<!-- <li class="active"><a href="currentOpen.php">Current Position</a></li> -->
      <!-- <li class="active"><a href="closePosition.php">Current Position</a></li> -->
			<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Search Data <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                	<li class="dropdown-header">Customer</li>
                  	<li><a href="editCustomerOpen.php" >Open Order</a></li>
                  	<li><a href="editCustomerClose.php">Close Order</a></li>
                  	<li class="divider"></li>
                  	<li class="dropdown-header">Company</li>
                  	<li><a href="editCompanyTransaction.php">Company Transaction</a></li>
                  	<li><a href="fairTransaction.php">Fair Component</a></li>
                </ul>
              </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Add <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  	<li><a href="AddLastTradingDay.php">Add Last Trading Day</a></li>
                  	<li><a href="addDividend.php">Add Dividend</a></li>
                    <li><a href="unpaid.php">Add Unpaid</a></li>
                  	<!-- <li><a href="unpaid.php">เพิ่มค่าใช้จ่าย ส่วนได้ / เสีย ยกมา</a></li> -->
                  	<li><a href="addMktID.php">เพิ่ม Marketing ID</a></li>
                  	<li><a href="addDailyRevenue.php">เพิ่ม Accounting and Fair</a></li>
                  	<li><a href="lastyearprofit.php">เพิื่มในวันสุดท้ายของปี</a></li>
                    <li><a href="tick.php">Add Tick</a></li>
                </ul>
             </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
          	<li><a href="reward.php">IC Incentive<span class="sr-only">(current)</span></a></li>
            <li class="active"><a href="logout.php">Logout <span class="sr-only">(current)</span></a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>