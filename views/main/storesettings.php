<div class="clearfix">
    <div class="x-panel-body panel-desc x-panel-body-noheader x-panel-body-noborder">
        <p>Here you can set the default attributes for all products created in this store.  These settings only affect <em>new</em> products. They have no effect on existing products.</p>
    </div>
    <div style="padding:20px;">

        <?php
        print \Formbuilder\Form::dropdown('StoreSettings[type]',$data['types'],$data['default.type'],array('label'=>'Product Type'));
        ?>

        <?php
        print \Formbuilder\Form::dropdown('StoreSettings[category]',$data['categories'],$data['default.category'],array('label'=>'Foxycart Category'));
        ?>
        <div class="store-track-inventory clearfix">
        <?php
        print \Formbuilder\Form::checkbox('StoreSettings[track_inventory]',$data['default.track_inventory'],array('label'=>'Track Inventory','description'=>'Should product inventory be tracked by default?'));
        ?>
        </div>
        
        <div class="store-custom-fields">
            <h3>Custom Fields</h3>
            <?php
            print \Formbuilder\Form::multicheck('StoreSettings[fields]',$data['fields'],$data['default.fields'],array());
            ?>        
            
            <h3 style="margin-top:20px;">Options</h3>

            <?php
            print \Formbuilder\Form::multicheck('StoreSettings[options]',$data['opts'],$data['default.options'],array());
            ?>
        </div>

    </div>
</div>