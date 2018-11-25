
<div id="icontact-box" class="icontact-box">
    <div class="icontact-box-button">
        <span class="icontact-envelope fa fa-envelope"></span> 
    </div>
    <div class="icontact-box-head" @click="icbShow = !icbShow" @keyup.space="icbShow = !icbShow">
        <span class="icontact-envelope fa fa-envelope"></span>
        <span>Email us</span>
        <span class="icontact-arrow fa fa-angle-down"></span>
        <!-- <span class="icontact-arrow fa" v-bind:class="icbShow ? 'fa-angle-up' : 'fa-angle-down'"></span> -->
    </div>
    <div class="icontact-box-content icontact-close" v-bind:class="icbShow ? '' : 'icontact-open'">
        <p v-if="errors.lenght">
            <ul style="margin: 0px !important;">
                <li v-for="error in errors">{{ error }}</li>
            </ul>
        </p>
        <form class="icontact-form" v-on:submit.prevent="submitForm">
            <input type="hidden" name="crf" v-model="crf">
            <div class="form-input">
                Topic:<select name="topic" v-model="topicInput">
                        <option disabled value="">Choose...</option>
                        <option v-for="data in topic" v-bind:value="data.value">{{ data.name }}</option>
                    </select>
            </div>
            <div class="form-input">
                Email:<input type="text" name="email" placeholder="Email" v-model="email">
            </div>
            <div class="form-input">
                Fullname:<input type="text" name="fullname" placeholder="Fullname" v-model="fullname">
            </div>
            <div class="form-input" v-bind:style="topicInput !== 'Other Concerns' ? {'display': 'none'} : ''">
                Subject:<input type="text" name="subject"  placeholder="Subject" v-model="subject">
            </div>
            <div class="form-input">
                Message:<textarea name="message" rows="10" placeholder="Message here..." v-model="message"></textarea>
            </div>
            <div class="form-input">
                <img v-bind:src="'data:image/png;charset=utf8;base64,'+recaptcha" alt='Captcha' style='margin-right: 5px;float: left;height: 30px; width: 100px;'>
                <input type="text" name="code" v-model="code" style="height: 30px;width: 100px;" v-bind:disabled="!formstatus"/>
            </div>
            <input type="submit" class="icontact-submit" value="Submit" v-bind:disabled="!formstatus" v-bind:style="!formstatus ? {'display':'none !important'} : ''">
            <div v-text="formloading" style="font-size: 12pt; margin: 5px 0px 5px 0px;" v-bind:style="formstatus ? {'display':'none !important'} : ''"></div>
        </form>
    </div>
</div>