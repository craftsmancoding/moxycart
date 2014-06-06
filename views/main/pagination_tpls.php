<?php

return array(
            'first' => '<a href="[+base_url+]&offset=[+offset+]" [+extra+]>&laquo; First</a>  ',
            'last' => ' <a href="[+base_url+]&offset=[+offset+]" [+extra+]>Last &raquo;</a>',
            'prev' => '<a href="[+base_url+]&offset=[+offset+]" [+extra+]>&lsaquo; Prev.</a> ',
            'next' => ' <a href="[+base_url+]&offset=[+offset+]" [+extra+]>Next &rsaquo;</a>',
            'current' => ' <span>[+page_number+]</span> ',
            'page' => ' <a href="[+base_url+]&offset=[+offset+]" [+extra+]>[+page_number+]</a> ',
            'outer'=>'<div id="pagination">[+content+]<div class="page-count">Page [+current_page+] of [+page_count+]</div><div class="displaying-page">Displaying records [+first_record+] thru [+last_record+] of [+record_count+]</div>'
);
