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
});
