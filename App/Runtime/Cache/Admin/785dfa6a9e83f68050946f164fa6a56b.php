<?php if (!defined('THINK_PATH')) exit();?><table>
<?php  foreach($attrdata as $v){ if($v['attr_type']==0){ if($v['attr_input_type']==0){ echo "<tr><td>".$v['attr_name']."</td><td>"; echo "<input type='text'  name='attr[".$v['id']."]'/>"; echo "</td></tr>"; }else{ echo "<tr><td>".$v['attr_name']."</td><td>"; echo "<select name='attr[".$v['id']."]'>"; $attrs = $v['attr_value']; $attrs = explode(',',$attrs); foreach($attrs as $v1){ echo "<option value='".$v1."'>".$v1."</option>"; } echo "</select>"; echo "</td></tr>"; } }else{ if($v['attr_input_type']==0){ echo "<tr><td>".$v['attr_name']."</td><td>"; echo "<input type='text'  name=''/>"; echo "</td></tr>"; }else{ echo "<tr><td><a href='javascript:' onclick='copythis(this)'>[+]</a>".$v['attr_name']."</td><td>"; echo "<select name='attr[".$v['id']."][]'>"; $attrs = $v['attr_value']; $attrs = explode(',',$attrs); foreach($attrs as $v1){ echo "<option value='".$v1."'>".$v1."</option>"; } echo "</select>"; echo "</td></tr>"; } } } ?>
</table>
<script>
    function  copythis(o){
       var curr_tr = $(o).parent().parent();
       if($(o).html()=='[+]'){
           var new_tr=curr_tr.clone();
           new_tr.find('a').html('[-]');
           curr_tr.after(new_tr);
       }else{
           curr_tr.remove();
       } 
    }
</script>