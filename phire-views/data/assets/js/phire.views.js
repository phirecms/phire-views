/**
 * Views Module Scripts for Phire CMS 2
 */

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
            }
        }
    }
});
