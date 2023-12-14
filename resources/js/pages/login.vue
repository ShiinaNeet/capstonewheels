<template>
    <div class="h-screen">
        <navigation />

        <div class="flex items-start min-h-[calc(100vh-62px)]">
            <div class="flex items-center w-9/12 py-8 min-h-[calc(100vh-62px)]" style="position: sticky; top:62px;">
                <div
                v-if="activeWindow === 'Login'"
                class="shrink m-auto w-[min(400px,75vw)]"
                id="login-form-wrapper"
                >
                    <h5 class="va-h5">
                        Welcome, Wheels User
                    </h5>
                    <h5 class="va-text-secondary">
                        Please login to continue
                    </h5>
                    <div class="mt-10">
                        <va-input
                        v-model="account.login.email"
                        type="email"
                        label="E-mail Address"
                        class="w-full mb-2 bg-[rgba(255,255,255,0.45)]"
                        :error="account.isInvalid"
                        :disabled="account.isLoading"
                        outline
                        />
                        <va-input
                        v-model="account.login.password"
                        :type="account.isPasswordVisible ? 'text' : 'password'"
                        label="Password"
                        class="w-full mb-3 bg-[rgba(255,255,255,0.45)]"
                        :error="account.isInvalid"
                        :error-messages="account.invalidMessage[0]"
                        :disabled="account.isLoading"
                        outline
                        >
                            <template #appendInner>
                                <va-icon
                                :name="account.isPasswordVisible ? 'visibility_off' : 'visibility'"
                                size="small"
                                color="#154EC1"
                                @click="account.isPasswordVisible = !account.isPasswordVisible"
                                />
                            </template>
                        </va-input>
                    </div>
                    <div class="mt-6 mb-6">
                        <va-button
                        class="w-full"
                        :loading="account.isLoading"
                        :disabled="account.isLoading"
                        @click="loginAccount()"
                        block
                        >
                            Log in
                        </va-button>
                    </div>
                    <div class="flex justify-between">
                        <a
                        class="va-link hover:underline"
                        href="#"
                        @click="forgotPassword.modal = !forgotPassword.modal"
                        >
                            Forgot password?
                        </a>
                        <span class="text-right">
                            No account?
                            <a
                            class="va-link hover:underline"
                            href="javascript:void(0)"
                            @click="
                                account.isValid = false,
                                account.isInvalid = false,
                                account.isLoading = false,
                                account.login.email = null,
                                account.login.password = null,
                                setActiveWindow('Register')
                            ">
                                Register now
                            </a>
                        </span>
                    </div>
                    <!-- <div class="my-8 text-gray-400">
                        <va-divider orientation="center">
                            <span class="px-2">OR</span>
                        </va-divider>
                    </div>
                    <div class="mt-6">
                        <va-button
                        class="w-full"
                        preset="secondary"
                        border-color="primary"
                        icon="facebook"
                        block
                        disabled
                        >
                            Log in with Facebook
                        </va-button>
                    </div> -->
                </div>
                <div
                v-if="activeWindow === 'Register'"
                class="shrink m-auto w-[min(400px,75vw)]"
                id="register-form-wrapper"
                >
                    <h5 class="va-h5">
                        Welcome, Guest User
                    </h5>
                    <h5 class="va-text-secondary">
                        Please register to continue
                    </h5>
                    <div class="mt-10">
                        <va-input
                        v-model="account.register.email"
                        type="email"
                        label="E-mail Address"
                        class="mb-2 w-full bg-[rgba(255,255,255,0.45)]"
                        requiredMark
                        :error="account.isInvalid && ((this.account.invalidMessage[1] != '') || (account.register.email == null || account.register.email == ''))"
                        :error-messages="account.invalidMessage[1]"
                        :success="account.isValid"
                        :messages="account.validMessage"
                        :readonly="account.isValid"
                        @update:modelValue="account.isInvalid = false"
                        outline
                        />
                    </div>
                    <div class="flex">
                        <va-checkbox
                        v-model="account.register.terms.checked"
                        class="mr-2 w-full z-[3]"
                        :error="account.register.terms.isInvalid"
                        :error-messages="account.register.terms.invalidMessage"
                        :disabled="account.register.saved || account.isValid"
                        @update:modelValue="account.register.terms.isInvalid = false"
                        />
                        <label class="absolute pt-[0.15rem] pl-[1.75rem] text-[15px] z-[1]">
                            I have read and agree to the
                            <a
                            class="va-link hover:underline"
                            href="javascript:void(0)"
                            @click="$root.redirectToPage('/terms')">
                                terms and condition
                            </a>
                        </label>
                    </div>
                    <div class="mt-6 mb-6">
                        <va-button
                        class="w-full"
                        :loading="account.isLoading"
                        :disabled="account.register.saved || account.isValid"
                        @click="account.register.saved = true, registerAccount()"
                        block
                        >
                            Register
                        </va-button>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-center">
                            Already have an account?
                            <a
                            class="va-link hover:underline"
                            href="javascript:void(0)"
                            @click="
                                account.isValid = false,
                                account.isInvalid = false,
                                account.isLoading = false,
                                account.register.email = null,
                                account.register.terms.checked = false,
                                account.register.terms.isInvalid = false,
                                setActiveWindow('Login')
                            ">
                                Log in
                            </a>
                        </span>
                    </div>
                    <!-- <div class="my-8 text-gray-400">
                        <va-divider orientation="center">
                            <span class="px-2">OR</span>
                        </va-divider>
                    </div>
                    <div class="mt-6">
                        <va-button
                        class="w-full"
                        preset="secondary"
                        border-color="primary"
                        icon="facebook"
                        block
                        disabled
                        >
                            Register with Facebook
                        </va-button>
                    </div> -->
                </div>
            </div>
            <div class="flex items-center w-1/4 pt-8 min-h-[calc(100vh-62px)]">
                <div class="mx-3 block text-center xl:w-[350px] xl:max-w-[350px] max-xl:w-[250px] max-xl:max-w-[250px] overflow-x-hidden">
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
                            <p class="leading-4 text-[15px] text-left va-text-secondary">
                                To become a leading private driving
                                institution in CALABARZON that will
                                give safety driving environment for
                                every motorist.
                            </p>
                        </div>
                        <div class="mb-[30px]">
                            <div class="va-title mb-2">
                                MISSION
                            </div>
                            <p class="leading-4 text-[15px] text-left va-text-secondary">
                                To train and educate quality
                                defensive  driving in accordance
                                with the LTO standards and other
                                regulatory bodies.
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
                                        {{ formatMobileNumber($root.config.contactNumberOne) }}
                                    </p>
                                </div>
                                <div class="flex justify-start">
                                    <va-icon
                                    class="shrink mx-[0.53em]"
                                    />
                                    <p class="shrink mx-2 va-text-secondary">
                                        {{ formatMobileNumber($root.config.contactNumberTwo) }}
                                    </p>
                                </div>
                                <div class="flex justify-start">
                                    <va-icon
                                    class="shrink mx-2"
                                    name="email"
                                    size="small"
                                    />
                                    <p class="shrink mx-2 text-[14px] va-text-secondary">
                                        {{ $root.config.contactEmail }}
                                    </p>
                                </div>
                                <div class="flex justify-start">
                                    <va-icon
                                    class="shrink mx-2"
                                    name="today"
                                    size="small"
                                    />
                                    <p class="shrink mx-2 text-[14px] va-text-secondary">
                                        Monday to Friday: 8:00am-5:00pm
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
        </div>
    </div>
    <va-modal
    v-model="forgotPassword.modal"
    @cancel="forgotPassword.data = {}"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Forgot Password
                </div>
                <va-input
                v-model="forgotPassword.email"
                type="email"
                label="E-mail Address"
                class="w-full mb-2"
                :rules="[(v) => v && v.length > 0 || 'The email field is required.']"
                :error="forgotPassword.isInvalid"
                :error-messages="forgotPassword.email ? forgotPassword.invalidMessage[0] : forgotPassword.invalidMessage[1]"
                :success="forgotPassword.isValid"
                :messages="forgotPassword.validMessage"
                :readonly="forgotPassword.isLoading || forgotPassword.isValid"
                @update:modelValue="forgotPassword.isInvalid = false, forgotPassword.invalidMessage[0] = ''"
                outline
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        v-if="!forgotPassword.isValid"
                        preset="secondary"
                        @click="
                            forgotPassword.modal = false,
                            forgotPassword.email = null,
                            forgotPassword.invalidMessage[2],
                            forgotPassword.isInvalid = false,
                            forgotPassword.isValid = false
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        :icon="!forgotPassword.isValid ? 'check' : ''"
                        :loading="forgotPassword.isLoading"
                        :disabled="forgotPassword.isLoading"
                        @click="
                            forgotPassword.isValid ? (
                                forgotPassword.modal = false,
                                forgotPassword.email = null,
                                forgotPassword.invalidMessage[0] = '',
                                forgotPassword.invalidMessage[2],
                                forgotPassword.isInvalid = false,
                                forgotPassword.isValid = false
                            ) : getForgotPassword()
                        ">
                            <p class="font-normal">
                                {{ forgotPassword.isValid ? 'Close' : 'Confirm' }}
                            </p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<style>
#banner picture.va-image__content img {
    border-radius: 4px;
}
</style>

<style scoped>
#login-form-wrapper::after,
#register-form-wrapper::after {
    content: "";
    position: absolute;
    top: 30px;
    bottom: 30px;
    right: 0px;
    border-right: 1px solid #DEE5F2;
}
.mapouter {
    position: relative;
    height: 250px;
    background: #FFF;
}
.maprouter a {
    color: #FFF!important;
    position: absolute!important;
    top: 0!important;
    z-index: 0!important;
}
.gmap_canvas {
    overflow:hidden;
    height: 250px;
}
.gmap_canvas iframe {
    position: relative;
    z-index: 2;
}
</style>

<script>
import navmenu from '@/components/navbar.vue';

export default {
    data () {
        return {
            account: {
                isValid: false,
                isInvalid: false,
                isLoading: false,
                validMessage: "",
                invalidMessage: [
                    "Please check your credentials and try again.",
                    "",
                ],
                isPasswordVisible: false,
                login: {
                    email: null,
                    password: null,
                },
                register: {
                    email: null,
                    invalidMessage: "The email field is required.",
                    terms: {
                        checked: false,
                        isInvalid: false,
                        invalidMessage: "You must agree to the terms to register.",
                    },
                    saved: false,
                },
            },
            forgotPassword:{
                modal: false,
                email: null,
                password: null,
                isLoading: false,
                isInvalid: false,
                validMessage: "",
                invalidMessage: [
                    "This email is not associated to any account.",
                    "The email field is required.",
                    "",
                ],
            },
            activeWindow: 'Login',
            loginDelay: 250,
        };
    },
    components: {
        navigation: navmenu,
    },
    methods: {
        formatMobileNumber(mobile) {
            const mobileWithoutLeadingZero = mobile.substring(1);
            return `(` + this.$root.config.contactCountryCode + `) ${mobileWithoutLeadingZero.slice(0, 3)} ${mobileWithoutLeadingZero.slice(3, 6)} ${mobileWithoutLeadingZero.slice(6)}`;
        },
        registerAccount() {
            this.account.isLoading = true;
            this.account.isValid = false;
            this.account.isInvalid = false;

            if (this.account.register.email && this.account.register.terms.checked) {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/register',
                    data: {
                        email: this.account.register.email,
                    },
                }).then(response => {
                    this.account.isLoading = false;

                    if (response.data.status == 1) {
                        // this.$root.prompt(response.data.text);
                        this.account.isValid = true;
                        this.account.validMessage = response.data.text;
                        this.account.isInvalid = false;

                        this.account.register.saved = false;
                    } else {
                        this.account.invalidMessage[1] = response.data.text;
                        this.account.isInvalid = true;

                        this.account.register.saved = false;
                    }
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    this.account.invalidMessage[1] = error.response.data.message;
                    this.account.isLoading = false;
                    this.account.isInvalid = true;

                    this.account.register.saved = false;
                });
            } else {
                this.account.isLoading = false;

                this.account.invalidMessage[1] = this.account.register.invalidMessage;
                this.account.isInvalid = true;

                if (!this.account.register.terms.checked) this.account.register.terms.isInvalid = true;

                this.account.register.saved = false;
            }
        },
        loginAccount() {
            this.account.isLoading = true;
            this.account.isInvalid = false;

            axios({
                method: 'POST',
                type: 'JSON',
                url: '/login',
                data: {
                    email: this.account.login.email,
                    password: this.account.login.password,
                }
            }).then(response => {
                this.account.isLoading = false;

                if (response.data.status == 1) {
                    setTimeout(() => {
                        window.location = response.data.redirect;
                    }, this.loginDelay);
                } else {
                    // this.$root.prompt(response.data.text);
                    this.account.isInvalid = true;
                }
            }).catch(error => {
                // this.$root.prompt(error.response.data.message);
                this.account.isLoading = false;
                this.account.isInvalid = true;
            });
        },
        setActiveWindow(window) {
            this.activeWindow = window;

            if (window === 'Login' && this.account.isValid) {
                this.account.isValid = false;
                this.account.validMessage = "";
                this.account.register.email = null;
            }
        },
        getForgotPassword(){
            this.forgotPassword.isLoading = true;
            this.forgotPassword.invalidMessage[0] && (this.forgotPassword.invalidMessage[0] = "This email is not associated to any account.");

            if (this.forgotPassword.email) {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/password',
                    data: {
                        email: this.forgotPassword.email,
                    }
                }).then(response => {
                    this.forgotPassword.isLoading = false;

                    if (response.data.status == 1) {
                        // this.$root.prompt(response.data.text);
                        this.forgotPassword.isValid = true;
                        this.forgotPassword.validMessage = "Password reset success, check your email for the password.";
                        this.forgotPassword.isInvalid = false;
                    } else this.forgotPassword.isInvalid = true;
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    this.forgotPassword.invalidMessage[0] = error.response.data.message;
                    this.forgotPassword.isLoading = false;
                    this.forgotPassword.isInvalid = true;
                });
            } else {
                this.forgotPassword.isLoading = false;
                this.forgotPassword.isInvalid = true;
            }
        },
    },
}
</script>
