/**
 * Views Module Scripts for Phire CMS 2
 */

phire.getViewModelTypes = function(sel) {
    phire.getModelTypes(sel);
    phire.selectViewModel(sel);
};

phire.selectViewModel = function(sel) {
    if (jax.cookie.load('phire') != '') {
        var phireCookie = jax.cookie.load('phire');
        var url = (jax('#id').val() != 0) ?
            phireCookie.base_path + phireCookie.app_uri + '/views/json/' + encodeURIComponent(jax(sel).val()) + '/' + jax('#id').val() :
            phireCookie.base_path + phireCookie.app_uri + '/views/json/' + encodeURIComponent(jax(sel).val());

        var json = jax.get(url);

        jax('#view-form-field-group-4 > dd:nth-child(2) > fieldset:first-child').remove();
        jax('#view-form-field-group-5 > dd:nth-child(2) > fieldset:first-child').remove();

        jax('#view-form-field-group-4 > dd:nth-child(2)').appendCheckbox(json.fields, {"name" : "group_fields[]", "id" : "group_fields"}, json.gMarked);
        jax('#view-form-field-group-5 > dd:nth-child(2)').appendCheckbox(json.fields, {"name" : "single_fields[]", "id" : "single_fields"}, json.sMarked);
    }
};

phire.selectViewModelType = function(sel) {
    if ((jax.cookie.load('phire') != '') && (jax('#model_1').val() != '----')) {
        var phireCookie = jax.cookie.load('phire');
        var type = jax(sel).val();

        var url = (type != '----') ?
            phireCookie.base_path + phireCookie.app_uri + '/views/json/' + encodeURIComponent(jax('#model_1').val()) + '/' + type.substring(type.indexOf('|') + 1) :
            phireCookie.base_path + phireCookie.app_uri + '/views/json/' + encodeURIComponent(jax('#model_1').val());

        var json = jax.get(url + ((jax('#id').val() != 0) ? '/' + jax('#id').val() : ''));

        jax('#view-form-field-group-4 > dd:nth-child(2) > fieldset:first-child').remove();
        jax('#view-form-field-group-5 > dd:nth-child(2) > fieldset:first-child').remove();
        jax('#view-form-field-group-4 > dd:nth-child(2)').appendCheckbox(json.fields, {"name" : "group_fields[]", "id" : "group_fields"}, json.gMarked);
        jax('#view-form-field-group-5 > dd:nth-child(2)').appendCheckbox(json.fields, {"name" : "single_fields[]", "id" : "single_fields"}, json.sMarked);
    }
};

jax(document).ready(function(){
    if (jax('#views-form')[0] != undefined) {
        jax('#checkall').click(function(){
            if (this.checked) {
                jax('#views-form').checkAll(this.value);
            } else {
                jax('#views-form').uncheckAll(this.value);
            }
        });
        jax('#views-form').submit(function(){
            return jax('#views-form').checkValidate('checkbox', true);
        });
    }

    if ((jax('#view-form')[0] != undefined) && (jax('#id').val() != 0)) {
        if (jax.cookie.load('phire') != '') {
            var phireCookie = jax.cookie.load('phire');
            var json = jax.get(phireCookie.base_path + phireCookie.app_uri + '/views/json/' + jax('#id').val());
            if (json.models.length > 0) {
                jax('#model_1').val(json.models[0].model);
                phire.getModelTypes(jax('#model_1')[0]);
                if ((json.models[0].type_field != null) && (json.models[0].type_value != null)) {
                    jax('#model_type_1').val(json.models[0].type_field + '|' + json.models[0].type_value);
                }
                json.models.shift();
                if (json.models.length > 0) {
                    phire.addModel(json.models);
                }
                phire.selectViewModel(jax('#model_1')[0]);
                phire.selectViewModelType(jax('#model_type_1')[0]);
            }
        }
    }
});
