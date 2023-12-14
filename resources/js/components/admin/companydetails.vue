<template>
    <div class="mx-5 mb-2 px-2.5 my-2.5 bg-white rounded flex-grid h-fit w-full"> 
        <div class="va-title mb-2 pt-2.5 pr-5">
            <h1> Driving School Information</h1>
        </div>
        <div class="vision mb-2 pr-5">
            <va-input
            type="textarea"
            class="w-full"
            v-model="companyDetails.data.vision"
            label="Vision"
            placeholder="Tell us about the Driving School mission."
            :min-rows="10"
            :max-rows="10"
            outline
            maxlength="1500"
            />
        </div>
        <div class="mission mb-2 pr-5">
            <va-input
            type="textarea"
            class="w-full"
            v-model="companyDetails.data.mission"
            label="Mission"
            placeholder="Tell us about the Driving School mission."
            :min-rows="10"
            :max-rows="10"
            outline
            maxlength="1500"
            />
        </div>
        <div class="contact mb-2 pr-5">
            <va-input
            type="textarea"
            class="w-full"
            v-model="companyDetails.data.contact_number"
            label="Contact Number"
            placeholder="Phone number here"
            outline
            maxlength="255"
            />
        </div>
        <div class="email mb-2 pr-5">
            <va-input
            type="textarea"
            class="w-full"
            v-model="companyDetails.data.email"
            label="Email Address"
            placeholder="School Address here"
            outline
            />
        </div>
        <div class="business hours mb-2 pr-5">
            <va-input
            type="textarea"
            class="w-full"
            v-model="companyDetails.data.business_hours"
            label="Business Hours"
            placeholder="School Address here"
            outline
            maxlength="255"
            />
        </div>
        <div class="terms mb-2 pr-5">
            <va-input
            type="textarea"
            class="w-full"
            v-model="companyDetails.data.terms"
            label="Terms and Conditions"
            :min-rows="10"
            :max-rows="10"
            outline
            maxlength="3000"
            />
        </div>
        <div>
            <va-button
            :loading="companyDetails.isButtonLoading"
            @click="insertSchoolInfo()"
            > 
                Save 
            </va-button>
        </div>
    </div>
</template>

<script>
import formatDate from '@/functions/formatdate.js';

const newCompanyDetails = {
    vision: '',
    mission: '',
    contact_number: '',
    email: '',
    business_hours: '',
    terms:'',
    id:1,
};
export default {
    data() {
        return {
            companyDetails: {
                isButtonLoading: false,
                data: {...newCompanyDetails},
            }, 
        };
    },
    mounted(){
        this.getCompanyInfo();
    },
    methods:{
        insertSchoolInfo(){
            this.companyDetails.isButtonLoading = true;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/company/save',
                data: this.companyDetails.data,
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                } else this.$root.prompt(response.data.text);
                this.companyDetails.isButtonLoading = false;
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.companyDetails.isButtonLoading = false;
            });
        },
        getCompanyInfo(){
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/company/get',
            }).then(response => {
                if (response.data.status == 1) {
                    this.companyDetails.data.vision         = response.data.result[0].vision;
                    this.companyDetails.data.mission        = response.data.result[0].mission;
                    this.companyDetails.data.contact_number = response.data.result[0].contact_number;
                    this.companyDetails.data.email          = response.data.result[0].email;
                    this.companyDetails.data.business_hours = response.data.result[0].business_hours; 
                    this.companyDetails.data.terms          = response.data.result[0].terms; 
                } else this.$root.prompt(response.data.text);

             
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
               
            });
        },
        formatDate
    },
};
</script>


