<?php
#########################################################
#                                                       #
#  CC / CREDIT CARD payment method class                #
#  This module is used for real time processing of      #
#  Credit card data of customers.                       #
#                                                       #
#  Copyright (c) 2009-2010 Novalnet AG                  #
#                                                       #
#  Released under the GNU General Public License        #
#  novalnet_cc module Created By Dixon Rajdaniel        #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version : novalnet_cc.php 1.3.1 2010-05-06   	    #
#                                                       #
#########################################################
?>
<script language="javascript"> window.onload = function(){document.forms["frm"].submit()} </script>
<div class="centerColumn" id="checkoutConfirmDefault">
		<form action="https://payport.novalnet.de/pci_payport" name="frm" id="frm" method="POST" target="nn_iframe">
			<?PHP 
				$newArray = $_SESSION['iFrame_params']['iframe_field'];
				foreach ($newArray as $k => $v) print "<input type='hidden' name=\"$k\" value=\"$v\" >";  
			?>
			<iframe height="450" width="100%" id="nn_iframe" name="nn_iframe" style="border:none;margin:auto;" src="<?php echo DIR_WS_IMAGES . 'novalnet_loading_img.gif'; ?>" >
				<p>Your browser does not support iframes.</p>
			</iframe>
		</form>
</div>