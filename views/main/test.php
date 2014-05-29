<script>
    function query_api() {
        var classname = $('#classname').find(":selected").text();
        var methodname = $('#methodname').find(":selected").text();    
        alert(classname);
    }

</script>
<div class="moxycart_canvas_inner">
	<h2 class="moxycart_cmp_heading" id="moxycart_heading">This is the Heading</h2>
</div>

<div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder" id="moxycart_content">
    <p>This is a page for testing the Moxycart REST API.</p>
    
    <span class="button btn moxycart-btn" onclick="javascript:mapi('product','test',{'moxycart_heading':'newtitle'});">Test Update Title</span>
    <form>
        <strong>Class:</strong>
        <select id="classname">
            <option>Asset</option>
            <option>Error</option>
            <option>Field</option>
            <option>OptionTerm</option>
            <option>OptionType</option>
            <option>Product</option>
            <option>Report</option>
            <option>Review</option>
        </select>
        
        <strong>Method:</strong>
        <select id="methodname">
            <option>Index</option>
            <option>Edit</option>
            <option>Create</option>
            <option>View</option>
            <option>Delete</option>
        </select>
        <br/>
        <span class="button btn moxycart-btn" onclick="javascript:query_api();">Query</span>
    </form>
    <br/>
    <strong>Response:</strong><br/>
    <textarea id="api_response" rows="20" cols="60"></textarea>
    
</div>