<?php
    global $data;
?>
<table width="100%" class="table">
    <tr style="border-left: 5px solid #8080ff; font-size: 200%;">
        <td width="5%" style="text-align: left;"><b><?php echo $data['tp_id']; ?></b></td>
        <td width="95%"><b><?php echo $data['tp_name']; ?></b></td>
    </tr>
    <tr style="border-left: 5px solid #8080ff; font-size: 150%;">
        <td colspan=2><?php echo $data['tp_description']; ?></td>
    </tr>     
</table>