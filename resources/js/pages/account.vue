<template>
    <div class="h-screen">
        <navigation />

        <div class="flex items-start min-h-[calc(100vh-62px)]">
            <div class="flex items-center w-full py-8 min-h-[calc(100vh-62px)]" style="position: sticky; top:62px;">
                <div class="shrink m-auto w-[min(500px,75vw)]">
                    <h5 class="text-lg mb-[15px]">
                        Account Details
                    </h5>
                    <div
                    v-if="!$root.auth.info"
                    class="mb-[15px]"
                    >
                        <va-alert
                        color="warning"
                        icon="warning"
                        class="mb-6"
                        >
                            To access the platform, please fill-in the necessary information.
                        </va-alert>
                    </div>
                    <div
                    v-if="account.updated.status"
                    class="mb-[15px]"
                    >
                        <va-alert
                        color="success"
                        icon="check_circle"
                        class="mb-6"
                        closeable
                        >
                            {{ account.updated.data.text }}
                        </va-alert>
                    </div>
                    <div
                    v-if="password.updated.status"
                    class="mb-[15px]"
                    >
                        <va-alert
                        color="info"
                        icon="info"
                        class="mb-6"
                        closeable
                        >
                            {{ password.updated.data.text }}
                        </va-alert>
                    </div>
                    <div class="w-full bg-gray-200 rounded-[3px] p-5 mb-[15px]">
                        <div class="flex flex-col gap-y-3">
                            <div class="flex justify-between">
                                <p>Your account type</p>
                                <span class="text-right font-bold">
                                    {{
                                        accountTypes[
                                            $root.arrayFind(
                                                accountTypes, (item) => item.value === $root.auth.userType
                                            )
                                        ].name
                                    }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <p>You joined on</p>
                                <span class="text-right font-bold">
                                    {{ formatDate($root.auth.createdAt, 'MMMM DD, YYYY') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-[3px] p-5">
                        <div class="flex flex-col gap-y-3">
                            <div class="flex justify-between">
                                <p>First name</p>
                                <span class="text-right font-bold">
                                    {{ account.hasInformation ? $root.auth.info.firstname : 'n/a' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <p>Last name</p>
                                <span class="text-right font-bold">
                                    {{ account.hasInformation ? $root.auth.info.lastname : 'n/a' }}
                                </span>
                            </div>
                            <div
                            v-if="$root.auth.userType === 0 || $root.auth.userType === 1"
                            class="flex justify-between"
                            >
                                <p>Birthdate</p>
                                <span class="text-right font-bold">
                                    {{ account.hasInformation ? formatDate($root.auth.info.birthdate, 'MMMM DD, YYYY', 'n/a') : 'n/a' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <p>Email address</p>
                                <span class="text-right font-bold">
                                    {{ $root.auth.email }}
                                </span>
                            </div>
                            <div class="flex w-full gap-x-3 mt-[10px]">
                                <div class="flex w-1/2 justify-between">
                                    <va-button
                                    preset="primary"
                                    :disabled="!$root.auth.info"
                                    @click="password.modal = !password.modal"
                                    >
                                        <p class="font-normal">Change password</p>
                                    </va-button>
                                </div>
                                <div class="flex w-1/2 justify-between">
                                    <va-button
                                    preset="primary"
                                    @click="
                                        account.data = (account.hasInformation ? { ...$root.auth.info } : { ...account.resetData }),
                                        account.modal = !account.modal
                                    ">
                                        <p class="font-normal">Update information</p>
                                    </va-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 mb-[20px]">
                        <div class="va-title text-neutral-400">
                            Input fields with
                            <span class="text-sm leading-[0.25rem]">*</span>
                            are required
                        </div>
                    </div>
                    <div class="w-full mb-[50px]">
                        If you have any questions, please reach out to us at
                        <a href="#" class="text-blue-700 hover:text-blue-500 hover:underline">Messenger on Facebook</a>
                        or at
                        <a href="#" class="text-blue-700 hover:text-blue-500 hover:underline">{{ $root.config.contactEmail }}</a>.
                    </div>
                    <va-button
                    preset="secondary"
                    class="mr-6 mb-2"
                    icon="logout"
                    @click="$root.redirectToPage('/logout')"
                    >
                        Logout
                    </va-button>
                </div>
            </div>
        </div>
    </div>

    <va-modal
    v-model="password.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Password
                </div>
                <va-input
                v-model="password.data.crtPass"
                :type="password.visible.crt ? 'text' : 'password'"
                label="Current Password"
                class="w-full mb-2"
                maxlength="120"
                :error="
                    password.crtpassEmpty ||
                    password.crtPassReentry ||
                    password.crtPassIncorrect ||
                    password.crtcfmPassMatch
                "
                :error-messages="errPassValid('crt')"
                @keyup="
                    password.crtpassEmpty = false,
                    password.newpassEmpty = false,
                    password.cfmpassEmpty = false,
                    password.newcfmMismatch = false,
                    password.newcfmNotMinPass = false,
                    password.crtPassIncorrect = false,
                    password.crtPassReentry = false,
                    password.crtcfmPassMatch = false
                ">
                    <template #appendInner>
                        <va-icon
                        :name="password.visible.crt ? 'visibility_off' : 'visibility'"
                        size="small"
                        color="#154EC1"
                        @click="password.visible.crt = !password.visible.crt"
                        />
                    </template>
                </va-input>
                <va-divider class="mb-3" />
                <va-input
                v-model="password.data.newPass"
                :type="password.visible.new ? 'text' : 'password'"
                label="New Password"
                class="w-full mb-2"
                :error="
                    password.crtpassEmpty ||
                    password.newpassEmpty ||
                    password.cfmpassEmpty ||
                    password.newcfmMismatch ||
                    password.newcfmNotMinPass ||
                    password.crtPassIncorrect ||
                    password.crtcfmPassMatch
                "
                @keyup="
                    password.crtpassEmpty = false,
                    password.newpassEmpty = false,
                    password.cfmpassEmpty = false,
                    password.newcfmMismatch = false,
                    password.newcfmNotMinPass = false,
                    password.crtPassIncorrect = false,
                    password.crtcfmPassMatch = false
                ">
                    <template #appendInner>
                        <va-icon
                        :name="password.visible.new ? 'visibility_off' : 'visibility'"
                        size="small"
                        color="#154EC1"
                        @click="password.visible.new = !password.visible.new"
                        />
                    </template>
                </va-input>
                <va-input
                v-model="password.data.cfmPass"
                :type="password.visible.cfm ? 'text' : 'password'"
                label="Confirm Password"
                class="w-full mb-2"
                maxlength="120"
                :error="
                    password.crtpassEmpty ||
                    password.newpassEmpty ||
                    password.cfmpassEmpty ||
                    password.newcfmMismatch ||
                    password.newcfmNotMinPass ||
                    password.crtPassIncorrect ||
                    password.crtcfmPassMatch
                "
                :error-messages="errPassValid('new')"
                @keyup="
                    password.crtpassEmpty = false,
                    password.newpassEmpty = false,
                    password.cfmpassEmpty = false,
                    password.newcfmMismatch = false,
                    password.newcfmNotMinPass = false,
                    password.crtPassIncorrect = false,
                    password.crtcfmPassMatch = false
                ">
                    <template #appendInner>
                        <va-icon
                        :name="password.visible.cfm ? 'visibility_off' : 'visibility'"
                        size="small"
                        color="#154EC1"
                        @click="password.visible.cfm = !password.visible.cfm"
                        />
                    </template>
                </va-input>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            password.data = { ...password.resetData },
                            password.crtpassEmpty = false,
                            password.newpassEmpty = false,
                            password.cfmpassEmpty = false,
                            password.visible.crt = false,
                            password.visible.new = false,
                            password.visible.cfm = false,
                            password.crtPassIncorrect = false,
                            password.crtPassReentry = false,
                            password.newcfmMismatch = false,
                            password.newcfmNotMinPass = false,
                            password.modal = !password.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="password.saved"
                        :disabled="password.saved"
                        @click="password.saved = true, updatePassword()"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="account.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
                <div class="va-title mb-3">
                    Update Information
                </div>
                <va-input
                v-model="account.email"
                type="email"
                label="E-mail Address"
                requiredMark
                class="w-full mb-2"
                disabled
                />
                <va-input
                v-model="account.data.firstname"
                label="Firstname"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The firstname field is required.']"
                :error="account.fnameEmpty"
                :error-messages="'The firstname field is required.'"
                @keyup="account.fnameEmpty = false"
                />
                <div class="flex w-full gap-x-1">
                    <div class="flex w-1/3 justify-between">
                        <va-input
                        v-model="account.data.middlename"
                        label="Middlename"
                        class="w-full mb-2"
                        maxlength="120"
                        />
                    </div>
                    <div class="flex w-1/3 justify-between">
                        <va-input
                        v-model="account.data.lastname"
                        label="Lastname"
                        requiredMark
                        class="w-full mb-2"
                        maxlength="120"
                        :rules="[(v) => v && v.length > 0 || 'Lastname field is required.']"
                        :error="account.lnameEmpty"
                        :error-messages="'Lastname field is required.'"
                        @keyup="account.lnameEmpty = false"
                        />
                    </div>
                </div>
                <va-input
                v-model="account.data.mobile"
                type="text"
                label="Mobile No."
                requiredMark
                class="w-full mb-2"
                :mask="{ phone: true, phoneRegionCode: 'ph' }"
                :rules="[(v) => v && v.length > 0 || 'The mobile field is required.']"
                :error="account.mobileEmpty"
                :error-messages="'The mobile field is required.'"
                @keyup="account.mobileEmpty = false"
                />
                <div v-if="$root.auth.userType === 0 || $root.auth.userType === 1">
                    <div class="flex w-full gap-x-1">
                        <div class="flex w-1/6 justify-between">
                            <va-select
                            v-model="account.data.gender"
                            class="w-full mb-2"
                            label="Gender"
                            requiredMark
                            :options="genders"
                            text-by="name"
                            value-by="value"
                            :error="account.genderEmpty"
                            :error-messages="'Gender field is required.'"
                            @update:modelValue="account.genderEmpty = false"
                            />
                        </div>
                        <div class="flex w-2/5 justify-between">
                            <va-date-input
                            v-model="account.data.birthdate"
                            label="Birthdate"
                            requiredMark
                            class="w-full mb-2"
                            :format="formatDatePicker"
                            :rules="[(v) => v && v.length > 0 || 'The birth date is required.']"
                            :error="account.bdateEmpty"
                            :error-messages="'The birth date is required.'"
                            @update:modelValue="account.bdateEmpty = false"
                            stickToEdges
                            />
                        </div>
                    </div>
                    <va-divider class="mb-3" />
                    <va-select
                    v-model="account.data.region"
                    class="w-full mb-2"
                    label="Region"
                    requiredMark
                    :options="geo.ph.regions"
                    text-by="region_name"
                    value-by="region_code"
                    searchable
                    clearable
                    clearable-icon="backspace"
                    highlight-matched-text
                    :error="account.regnEmpty"
                    :error-messages="'Region field is required.'"
                    @update:modelValue="
                        account.regnEmpty = false,
                        account.provEmpty = false,
                        account.cityEmpty = false,
                        account.brgyEmpty = false,
                        account.data.province && (
                            account.data.province = '',
                            account.data.city = '',
                            account.data.barangay = '',
                            geo.ph.cities = [],
                            geo.ph.barangays = []
                        ),
                        getProvince(account.data.region)
                    "/>
                    <div class="flex w-full gap-x-1">
                        <div class="flex w-1/4 justify-between">
                            <va-select
                            v-model="account.data.province"
                            class="w-full mb-2"
                            label="Province"
                            requiredMark
                            :options="geo.ph.provinces"
                            text-by="province_name"
                            value-by="province_code"
                            searchable
                            highlight-matched-text
                            :error="account.provEmpty"
                            :error-messages="'Province is required.'"
                            :no-options-text="'Select a region for option(s)'"
                            @update:modelValue="
                                account.provEmpty = false,
                                account.data.city && (
                                    account.data.city = '',
                                    account.data.barangay = '',
                                    geo.ph.barangays = []
                                ),
                                getCity(account.data.province)
                            "/>
                        </div>
                        <div class="flex w-1/4 justify-between">
                            <va-select
                            v-model="account.data.city"
                            class="w-full mb-2"
                            label="City"
                            requiredMark
                            :options="geo.ph.cities"
                            text-by="city_name"
                            value-by="city_code"
                            searchable
                            highlight-matched-text
                            :error="account.cityEmpty"
                            :error-messages="'City is required.'"
                            :no-options-text="'Select a province for option(s)'"
                            @update:modelValue="
                                account.cityEmpty = false,
                                account.data.barangay && (account.data.barangay = ''),
                                getBarangay(account.data.city)
                            "/>
                        </div>
                        <div class="flex w-1/4 justify-between">
                            <va-select
                            v-model="account.data.barangay"
                            class="w-full mb-2"
                            label="Barangay"
                            requiredMark
                            :options="geo.ph.barangays"
                            text-by="brgy_name"
                            value-by="brgy_code"
                            searchable
                            highlight-matched-text
                            :error="account.brgyEmpty"
                            :error-messages="'Barangay is required.'"
                            :no-options-text="'Select a city for option(s)'"
                            @update:modelValue="account.brgyEmpty = false"
                            />
                        </div>
                    </div>
                    <va-input
                    v-model="account.data.address"
                    label="Street / Building / House No."
                    requiredMark
                    class="w-full mb-2"
                    :rules="[(v) => v && v.length > 0 || 'The address field is required.']"
                    :error="account.addrEmpty"
                    :error-messages="'The address field is required.'"
                    @keyup="account.addrEmpty = false"
                    />
                    <va-divider class="mb-3" />
                    <div class="flex w-full gap-x-1">
                        <div class="flex w-1/3 justify-between">
                            <va-input
                            v-model="account.data.license_no"
                            label="License no."
                            class="w-full mb-2"
                            :mask="{ blocks: [3, 2, 6], uppercase: true, delimiter: '-' }"
                            @keyup="!account.data.license_no && (account.data.expiration_date = null, account.data.restriction_codes = [])"
                            />
                        </div>
                        <div class="flex w-2/4 justify-between">
                            <va-date-input
                            v-model="account.data.expiration_date"
                            label="Expiration Date"
                            :requiredMark="!licenseValid(account.data.license_no)"
                            class="w-full mb-2"
                            :disabled="licenseValid(account.data.license_no)"
                            :format="formatDatePicker"
                            :rules="[(v) => v && v.length > 0 || 'The expiration date is required.']"
                            :error="account.edateEmpty"
                            :error-messages="'The expiration date is required.'"
                            @keyup="account.edateEmpty = false"
                            stickToEdges
                            />
                        </div>
                    </div>
                    <va-select
                    v-model="account.data.restriction_codes"
                    class="w-full mb-2"
                    :label="account.data.restriction_codes_old ? 'Restriction Code(s) Old' : 'Restriction Code(s) New'"
                    :requiredMark="!licenseValid(account.data.license_no)"
                    :options="restriction_codes[account.data.restriction_codes_old ? 'old' : 'new']"
                    text-by="name"
                    value-by="value"
                    multiple
                    searchable
                    clearable
                    clearable-icon="backspace"
                    highlight-matched-text
                    :disabled="licenseValid(account.data.license_no)"
                    :rules="[(v) => v && v.length > 0 || 'The expiration date is required.']"
                    :error="account.rcodesEmpty"
                    :error-messages="'The restriction codes field is required.'"
                    @keyup="account.rcodesEmpty = false"
                    @update:modelValue="account.data.restriction_codes.sort()"
                    >
                        <template #append>
                            <va-icon
                            :name="account.data.restriction_codes_old ? '123' : 'abc'"
                            size="32px"
                            :color="account.data.restriction_codes_old ? '#154EC1' : '#767c88'"
                            @click="account.data.restriction_codes = [], account.data.restriction_codes_old = !account.data.restriction_codes_old"
                            />
                        </template>
                    </va-select>
                </div>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="
                            account.emailEmpty = false,
                            account.fnameEmpty = false,
                            account.lnameEmpty = false,
                            account.mobileEmpty = false,
                            account.bdateEmpty = false,
                            account.genderEmpty = false,
                            account.addrEmpty = false,
                            account.brgyEmpty = false,
                            account.cityEmpty = false,
                            account.provEmpty = false,
                            account.regnEmpty = false,
                            account.licenseEmpty = false,
                            account.edateEmpty = false,
                            account.rcodesEmpty = false,
                            this.$root.auth.info && getGeoLocation(this.$root.auth.info),
                            account.modal = !account.modal
                        ">
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="save"
                        :loading="account.saved"
                        :disabled="account.saved"
                        @click="account.saved = true, insertUpdateAccDetails()"
                        >
                            <p class="font-normal">Save</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </template>
    </va-modal>
</template>

<script>
import navmenu from '@/components/navbar.vue';
import { regions, provinces, cities, barangays } from 'select-philippines-address';
import formatDate from '@/functions/formatdate.js';
import 'cleave.js/dist/addons/cleave-phone.ph.js';

const newAccountInformation = {
    firstname: "",
    middlename: "",
    lastname: "",
    mobile: "",
    birthdate: null,
    gender: null,
    address: "",
    barangay: "",
    city: "",
    province: "",
    region: "",
    license_no: "",
    expiration_date: null,
    restriction_codes: [],
    restriction_codes_old: false,
}

const updPassword = {
    crtPass: "",
    newPass: "",
    cfmPass: "",
}

export default {
    data () {
        return {
            account: {
                modal: false,
                emailEmpty: false,
                fnameEmpty: false,
                lnameEmpty: false,
                mobileEmpty: false,
                bdateEmpty: false,
                genderEmpty: false,
                addrEmpty: false,
                brgyEmpty: false,
                cityEmpty: false,
                provEmpty: false,
                regnEmpty: false,
                licenseEmpty: false,
                edateEmpty: false,
                rcodesEmpty: false,
                hasInformation: false,
                updated: {
                    status: false,
                    data: {},
                },
                email: "",
                saved: false,
                resetData: { ...newAccountInformation },
                data: {},
            },
            password: {
                modal: false,
                crtpassEmpty: false,
                newpassEmpty: false,
                cfmpassEmpty: false,
                updated: {
                    status: false,
                    data: {},
                },
                visible: {
                    crt: false,
                    new: false,
                    cfm: false,
                },
                crtcfmPassMatch: false,
                crtPassIncorrect: false,
                crtPassReentry: false,
                newcfmMismatch: false,
                newcfmNotMinPass: false,
                saved: false,
                resetData: { ...updPassword },
                data: {},
            },
            restriction_codes: {
                new: [
                    { name: "A", value: 0 },
                    { name: "A1", value: 1 },
                    { name: "B", value: 2 },
                    { name: "B1", value: 3 },
                    { name: "B2", value: 4 },
                    { name: "C", value: 5 },
                    { name: "D", value: 6 },
                    { name: "BE", value: 7 },
                    { name: "CE", value: 8 },
                ],
                old: [
                    { name: "1", value: 0 },
                    { name: "2", value: 1 },
                    { name: "3", value: 2 },
                    { name: "4", value: 3 },
                    { name: "5", value: 4 },
                    { name: "6", value: 5 },
                    { name: "7", value: 6 },
                    { name: "8", value: 7 },
                ],
            },
            accountTypes: [
                { name: "Student", value: 0 },
                { name: "Instructor", value: 1 },
                { name: "Secretary", value: 2 },
                { name: "Administrator", value: 3 },
            ],
            genders: [
                { name: "Female", value: 0 },
                { name: "Male", value: 1 },
            ],
            geo: {
                ph: {
                    regions: [],
                    provinces: [],
                    cities: [],
                    barangays: [],
                },
            },
        }
    },
    components: {
        navigation: navmenu,
    },
    created () {
        regions().then(response => {
            this.geo.ph.regions = response;
        });
    },
    beforeMount () {
        this.genders.reverse();
        this.account.email = this.$root.auth.email;
        if (this.$root.auth && !this.$root.auth.info) {
            this.account.hasInformation = false;

            this.account.data = { ...this.account.resetData };
            this.account.modal = true;
        } else this.account.hasInformation = true;

        if (localStorage.getItem('/account/details_update') !== null) {
            this.account.updated.status = true;

            this.account.updated.data = JSON.parse(localStorage.getItem('/account/details_update'));
            localStorage.clear();
        }

        if (this.account.hasInformation) this.getGeoLocation(this.$root.auth.info);
    },
    methods: {
        updatePassword() {
            this.password.updated.status && (this.password.updated.status = false, this.password.updated.data = {});
            this.account.updated.status && (this.account.updated.status = false);
            if (this.password.data.crtPass && ((this.password.data.newPass && this.password.data.cfmPass) && (this.password.data.newPass === this.password.data.cfmPass))) {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/account/password_update',
                    data: {
                        password: {
                            crt: this.password.data.crtPass,
                            new: this.password.data.newPass,
                        },
                    }
                }).then(response => {
                    // this.$root.prompt(response.data.text);
                    if (response.data.status == 1) {
                        this.password.modal = false;

                        if (!this.$root.config.strictPasswordUpdate) {
                            this.password.data = { ...this.password.resetData };
                        }
                        this.password.updated.status = true;
                        this.password.updated.data = response.data;
                    } else if (response.data.status == 2) {
                        this.password.crtcfmPassMatch = true;

                        if (!this.$root.config.strictPasswordUpdate) {
                            this.password.data = { ...this.password.resetData };
                        }
                    } else {
                        this.password.crtPassIncorrect = true;

                        if (!this.$root.config.strictPasswordUpdate) {
                            this.password.data.newPass = "";
                            this.password.data.cfmPass = "";
                        }
                    }

                    if (this.$root.config.strictPasswordUpdate) {
                        this.password.data = { ...this.password.resetData };
                    }
                    this.password.saved = false;
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    // if (resDataError.filter(key => key == 'password.crt').length)
                    //     this.password.crtpassEmpty = true;

                    if (resDataError.filter(key => key == 'password.new').length) {
                        if (this.$root.config.strictPasswordUpdate)
                            this.password.crtPassReentry = true;
                        this.password.newcfmNotMinPass = true;
                    }

                    if (this.$root.config.strictPasswordUpdate) {
                        this.password.data = { ...this.password.resetData };
                    }

                    this.password.saved = false;
                });
            } else {
                if (!this.password.data.crtPass)
                    this.password.crtpassEmpty = true;
                else
                    if (this.$root.config.strictPasswordUpdate)
                        this.password.crtPassReentry = true;

                if (!this.password.data.newPass || !this.password.data.cfmPass) {
                    this.password.newpassEmpty = true;
                    this.password.cfmpassEmpty = true;
                    if (!this.$root.config.strictPasswordUpdate) {
                        this.password.data.newPass = "";
                        this.password.data.cfmPass = "";
                    }
                } else {
                    if (this.password.data.newPass !== this.password.data.cfmPass)
                        this.password.newcfmMismatch = true;

                    if (this.password.data.newPass.length < this.$root.config.minPasswordChars && this.password.data.cfmPass.length < this.$root.config.minPasswordChars)
                        this.password.newcfmNotMinPass = true;
                }

                if (this.$root.config.strictPasswordUpdate) {
                    this.password.data = { ...this.password.resetData };
                }
                this.password.saved = false;
            }
        },
        insertUpdateAccDetails() {
            this.account.updated.status && (this.account.updated.status = false);
            this.password.updated.status && (this.password.updated.status = false, this.password.updated.data = {});

            const data = { ...this.account.data };
            data.birthdate !== null && (data.birthdate = this.formatDate(data.birthdate, 'YYYY-MM-DD'));
            data.expiration_date !== null && (data.expiration_date = this.formatDate(data.expiration_date, 'YYYY-MM-DD'));
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/account/details_update',
                data: data
            }).then(response => {
                // this.$root.prompt(response.data.text);
                if (response.data.status == 1) {
                    this.account.saved && (
                        this.account.emailEmpty = false,
                        this.account.fnameEmpty = false,
                        this.account.lnameEmpty = false,
                        this.account.mobileEmpty = false,
                        this.account.bdateEmpty = false,
                        this.account.genderEmpty = false,
                        this.account.addrEmpty = false,
                        this.account.brgyEmpty = false,
                        this.account.cityEmpty = false,
                        this.account.provEmpty = false,
                        this.account.regnEmpty = false,
                        this.account.licenseEmpty = false,
                        this.account.edateEmpty = false,
                        this.account.rcodesEmpty = false
                    );

                    localStorage.setItem('/account/details_update',
                        JSON.stringify(response.data)
                    );
                    location.reload();
                    // this.account.saved = false;
                }
                else{
                    this.$root.prompt(response.data.text);
                    this.account.saved = false
                }  
            }).catch(error => {
                // this.$root.prompt(error.response.data.message);
                let resDataError = Object.keys(error.response.data.errors);

                if (resDataError.filter(key => key == 'email').length)
                    this.account.emailEmpty = true;
                if (resDataError.filter(key => key == 'firstname').length)
                    this.account.fnameEmpty = true;
                if (resDataError.filter(key => key == 'lastname').length)
                    this.account.lnameEmpty = true;
                if (resDataError.filter(key => key == 'mobile').length)
                    this.account.mobileEmpty = true;
                if (resDataError.filter(key => key == 'gender').length)
                    this.account.genderEmpty = true;
                if (resDataError.filter(key => key == 'birthdate').length)
                    this.account.bdateEmpty = true;
                if (resDataError.filter(key => key == 'address').length)
                    this.account.addrEmpty = true;
                if (resDataError.filter(key => key == 'barangay').length)
                    this.account.brgyEmpty = true;
                if (resDataError.filter(key => key == 'city').length)
                    this.account.cityEmpty = true;
                if (resDataError.filter(key => key == 'province').length)
                    this.account.provEmpty = true;
                if (resDataError.filter(key => key == 'region').length)
                    this.account.regnEmpty = true;
                if (resDataError.filter(key => key == 'expiration_date').length)
                    this.account.edateEmpty = true;
                if (resDataError.filter(key => key == 'restriction_codes').length)
                    this.account.rcodesEmpty = true;

                this.account.saved = false
            });
        },
        licenseValid(string) {
            // return (!string || string.length !== 11);
            return !string;
        },
        errPassValid(string) {
            if (string === 'crt') {
                string = "The current password ";

                if (this.password.crtPassIncorrect)
                    string += "is incorrect.";
                else {
                    if (this.password.crtPassReentry)
                        string += "needs to be entered again.";
                    else
                        string += "is required.";
                }

                if (this.password.crtcfmPassMatch)
                    string = "";
            }
            if (string === 'new') {
                string = "The new and confirm password ";

                if (this.password.newpassEmpty && this.password.cfmpassEmpty)
                    string += "field is required.";
                else {
                    if (this.password.newcfmMismatch)
                        string += " does not match.";
                    else {
                        if (!this.password.crtpassEmpty) {
                            if (this.password.newcfmNotMinPass)
                                string = "The new password must be at least " + this.$root.config.minPasswordChars + " characters.";
                            else
                                string += "needs to be entered again.";

                            } else {
                            if (this.password.crtPassIncorrect || !this.password.crtPassReentry)
                                string += "needs to be entered again.";

                        }
                    }
                }

                if (this.password.crtcfmPassMatch)
                    string = "The current password can't be used as a new password.";
            }
            return string;
        },
        getGeoLocation(string) {
            string.region && this.getProvince(string.region);
            string.province && this.getCity(string.province);
            string.city && this.getBarangay(string.city);
        },
        getProvince(string) {
            provinces(string).then(response => {
                this.geo.ph.provinces = response;
            });
        },
        getCity(string) {
            cities(string).then(response => {
                this.geo.ph.cities = response;
            });
        },
        getBarangay(string) {
            barangays(string).then(response => {
                this.geo.ph.barangays = response;
            });
        },
        formatDatePicker(date) {
            return this.formatDate(date, 'MMMM DD, YYYY', 'Invalid Date');
        },
        formatDate
    }
}
</script>
