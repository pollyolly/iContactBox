var icb_j = jQuery.noConflict();
var icontactBox = new Vue({
    el: '#icontact-box',
    data: {
        topic: [
            {name:'Demo1', value:'Demo1'},
            {name:'Demo2', value:'Demo2'},
            {name:'Demo3', value:'Demo3'},
            {name:'Demo4', value:'Demo4'},
            {name:'Demo5', value:'Demo5'},
            {name:'Demo6', value:'Demo6'}
        ],
        topicInput:'',
        email: '',
        fullname: '',
        subject: '',
        message: '',
        icbShow: true,
        errors: ['Please fill the blanks.'],
        formloading: '',
        formstatus: true,
        recaptcha: '',
        code: '',
        crf: ''
    },
    mounted: function(){
        this.reloadCaptcha();
    }, 
    methods: {
        submitForm: function(){
            this.errors = [];
            if(!this.code){this.errors.push("Captcha is empty.");}
            if(this.topicInput == 'Other Concerns'){
                if(!this.subject){
                    this.errors.push("Subject field is empty.");
                }
            }
            if(!this.topicInput){this.errors.push("Please select a topic.");}
            if(!this.email){this.errors.push("Email field is empty.");} 
            else if(!this.validateEmail(this.email)){this.errors.push("Invalid email.");}
            if(!this.fullname){this.errors.push("Fullname field is empty.")}
            if(!this.message){this.errors.push("Message field is empty.")}
            if(this.code && this.topicInput && this.email && this.message && this.validateEmail(this.email)){
                this.formstatus = false;
                this.formloading = 'Please wait...';
                icb_j.ajax({
                    type: "POST",
                    url: ajaxUrl.ajax_url,
                    data: {
                        action: 'icb_form_save',
                        icb_topic: this.topicInput == 'Other Concerns' ? this.subject : this.topicInput,
                        icb_email: this.email,                        
                        icb_fullname: this.fullname,
                        icb_message: this.message,
                        icb_code: this.code,
                        icb_crf_code: this.crf
                    },
                    cache: false,
                    datatype: "JSON",
                    success: function(data){
                        let j = JSON.parse(data);
                        icontactBox.formstatus = true;
                        icontactBox.topicInput = '';
                        icontactBox.email = '';
                        icontactBox.fullname = '';
                        icontactBox.subject = '';
                        icontactBox.code ='';
                        icontactBox.errors.push(j[0]);
                        icontactBox.reloadCaptcha();
                        console.log(data);
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
            }
        },
        validateEmail: function(email){
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },
        reloadCaptcha: function(){
            icb_j.ajax({
                type: "POST",
                url: ajaxUrl.ajax_url,
                data: {
                    action: 'icb_getcaptcha_session'
                },
                cache: false,
                datatype: 'JSON',
                success: function(data){
                    let jdata = JSON.parse(data);
                    icontactBox.recaptcha = jdata.image;
                    icontactBox.crf = jdata.crf;
                },
                error: function(data){
                    console.log(data);
                }
            });
        }
    }
});
