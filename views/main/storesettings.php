<div class="clearfix">
    <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
        <p>Here you can set the default attributes for all products created in this store.  These settings only affect <em>new</em> products. They have no effect on existing products.</p>
    </div>
    
    <div style="padding:20px;">
        <?php
        print \Formbuilder\Form::dropdown('StoreSettings[template_id]',$data['templates'],'',array('label'=>'Template'));
        ?>
    
        <?php
        print \Formbuilder\Form::dropdown('StoreSettings[type]',$data['types'],'',array('label'=>'Product Type'));
        ?>

        <?php
        print \Formbuilder\Form::dropdown('StoreSettings[category]',$data['categories'],'',array('label'=>'Foxycart Category'));
        ?>

        <?php
        print \Formbuilder\Form::dropdown('StoreSettings[track_inventory]',array('0'=>'No','1'=>'Yes'),$data['track_inventory'],array('label'=>'Track Inventory','description'=>'Should product inventory be tracked by default?'));
        ?>

        <h3>Custom Fields</h3>
        <?php
        print \Formbuilder\Form::multicheck('StoreSettings[fields]',$data['fields'],'',array());
        ?>        
        
        <h3>Options</h3>
        <?php
        print \Formbuilder\Form::multicheck('StoreSettings[options]',$data['opts'],'',array());
        ?>

    </div>
</div>