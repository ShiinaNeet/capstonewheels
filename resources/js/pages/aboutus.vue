<template>
    <navigation />
    <div class="flex-center place-items-center w-full pt-8 min-h-[calc(100vh-500px)]">
                <div class="mx-3 block text-center xl:w-[550px] xl:max-w-[550px] max-xl:w-[550px] max-xl:max-w-[550px] overflow-x-hidden">
                    <h5 class="text-base">
                        About Us
                    </h5>
                    <div class="mt-4">
                        <div class="mb-[15px]">
                            <va-image
                            id="banner"
                            class="col-span-1"
                            :ratio="16/9"
                            src="../../../images/about_us.jpg"
                            />
                        </div>
                        <div class="mb-[15px]">
                            <div class="va-title mb-2">
                                VISION
                            </div>
                            <p class="leading-4 text-[15px] text-center va-text-secondary">
                                {{ companyInfo.data.length > 0 ? companyInfo.data[0].vision : ''}}
                            </p>
                        </div>
                        <div class="mb-[30px]">
                            <div class="va-title mb-2">
                                MISSION
                            </div>
                            <p class="leading-4 text-[15px] text-center va-text-secondary">
                                {{ companyInfo.data.length > 0 ? companyInfo.data[0].mission : ''}}
                            </p>
                        </div>
                        <div class="mb-[15px]">
                            <div class="va-title mb-2">
                                BUSINESS INFORMATION
                            </div>
                            <div>
                                <div class="flex justify-start">
                                    <va-icon
                                    class="shrink mx-2"
                                    name="call"
                                    size="small"
                                    />
                                    <p class="shrink mx-2 va-text-secondary">
                                        {{companyInfo.data.length > 0 ? companyInfo.data[0].contact_number : ''}}
                                    </p>
                                    
                                </div>
                                <div class="flex justify-start">
                                    <va-icon
                                    class="shrink mx-2"
                                    name="email"
                                    size="small"
                                    />
                                    <p class="shrink mx-2 text-[14px] va-text-secondary">
                                        {{ companyInfo.data.length > 0 ? companyInfo.data[0].email : '' }}
                                    </p>
                                </div>
                                <div class="flex justify-start">
                                    <va-icon
                                    class="shrink mx-2"
                                    name="today"
                                    size="small"
                                    />
                                    <p class="shrink mx-2 text-[14px] va-text-secondary">
                                        {{ companyInfo.data.length > 0 ? companyInfo.data[0].business_hours : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="va-title mb-2">
                                LOCATION
                            </div>
                            <div class="mapouter xl:w-[350px] xl:max-w-[350px] max-xl:w-[250px] max-2xl:max-w-[250px]">
                                <div class="gmap_canvas xl:w-[350px] xl:max-w-[350px] max-xl:w-[250px] max-2xl:max-w-[250px]">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2302.4330601997617!2d120.73019820236568!3d13.945775632671861!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bda2df27af0b21%3A0x287b322b0185855f!2sWPWJ%2BF2F%2C%20Balayan%2C%20Batangas!5e0!3m2!1sen!2sph!4v1699986312122!5m2!1sen!2sph" width="350" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</template>

<script>
import navmenu from '@/components/navbar.vue';



export default {
    data(){
        return{
            companyInfo: {
                data:{},
            },
        }
    },
    mounted() {
        this.getCompanyInfo();
    },
    methods: {
        formatMobileNumber(mobile) {
            const mobileWithoutLeadingZero = mobile.substring(1);
            return `(` + this.$root.config.contactCountryCode + `) ${mobileWithoutLeadingZero.slice(0, 3)} ${mobileWithoutLeadingZero.slice(3, 6)} ${mobileWithoutLeadingZero.slice(6)}`;
        },
        getCompanyInfo(){
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/company'
            }).then(response => {
                if (response.data.status == 1) {
                    this.companyInfo.data = response.data.result;
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
            });
        }
    },
    components: {
       navigation: navmenu
    }
};
</script>

<style>
#banner picture.va-image__content img {
    border-radius: 4px;
}
</style>