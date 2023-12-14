<template #content>
    <navigation />
    <div class="flex-center py-5">
        <div class="place-self-center w-[410px] p-5 ">
            <div class="leading-5 text-[#555]">
                <p class="text-[18px] mb-3">
                    <va-icon
                    class="mr-1"
                    name="add_ic_call"
                    size="22px"
                    />
                    Contact us through the following...
                </p>
                <p class="mb-4">
                    <a href="#" class="text-blue-700 hover:text-blue-500 hover:underline">{{ $root.config.contactEmail }}</a>
                    with your inquiries during our business hours, 8 AM to 5 PM, Monday through Saturday.
                </p>
                <p class="text-[15px] text-right mb-2">
                    You may also use this form below.
                    <va-icon
                    name="move_to_inbox"
                    size="small"
                    />
                </p>
            </div>
            <va-divider />
            <div v-if="!createInquiry.submitted">
                <div class="va-title mb-3">
                    Inquiry Form
                </div>
                <va-input
                v-model="createInquiry.data.senderEmail"
                label="Email Address"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The email address field is required.']"
                :error="createInquiry.senderemailEmpty"
                :error-messages="'The email address field is required.'"
                @keyup="createInquiry.senderemailEmpty = false"
                />
                <va-input
                v-model="createInquiry.data.subject"
                label="Subject"
                requiredMark
                class="w-full mb-2"
                maxlength="120"
                :rules="[(v) => v && v.length > 0 || 'The subject field is required.']"
                :error="createInquiry.subjectEmpty"
                :error-messages="'The subject field is required.'"
                @keyup="createInquiry.subjectEmpty = false"
                />
                <va-input
                type="textarea"
                v-model="createInquiry.data.inquiry"
                label="Inquiry"
                requiredMark
                class="w-full mb-2 force-noresize"
                maxlength="255"
                :counter="!!(createInquiry.data.inquiry && createInquiry.data.inquiry.length > 0)"
                :max-length=255
                :rules="[(v) => v && v.length > 0 || 'The inquiry field is required.']"
                :error="createInquiry.inquiryEmpty"
                :error-messages="'The inquiry field is required.'"
                @keyup="createInquiry.inquiryEmpty = false"
                :min-rows="10"
                :max-rows="10"
                />
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="resetForm()"
                        >
                            <p class="font-normal">Cancel</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        :loading="createInquiry.saved"
                        :disabled="createInquiry.saved"
                        @click="insertUpdateInquiry()"
                        >
                            <p class="font-normal">Submit</p>
                        </va-button>
                    </div>
                </div>
            </div>
            <div v-else>
                <va-alert
                color="secondary"
                icon="forward_to_inbox"
                class="mb-6"
                >
                    Inquiry has been recorded
                </va-alert>
                <div class="flex w-full gap-x-3 mt-[15px]">
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        preset="secondary"
                        @click="resetForm()"
                        >
                            <p class="font-normal">Close</p>
                        </va-button>
                    </div>
                    <div class="flex w-1/2 justify-between">
                        <va-button
                        icon="check"
                        disabled
                        >
                            <p class="font-normal">Submit</p>
                        </va-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import navmenu from '@/components/navbar.vue';

const newInq = {
    subject: '',
    inquiry: '',
    senderEmail: '',
};

export default {
    data() {
        return {
            createInquiry: {
                modal: false,
                subjectEmpty: false,
                inquiryEmpty: false,
                senderemailEmpty: false,
                submitted: false,
                saved: false,
                done: {
                    delay: 500,
                },
                data: { ...newInq }
            }
        };
    },
    methods: {
        resetForm() {
            // Reset the form to its initial state
            this.createInquiry.data = { ...this.createInquiry.resetData };
            this.createInquiry.subjectEmpty = false;
            this.createInquiry.inquiryEmpty = false;
            this.createInquiry.senderemailEmpty = false;
            this.createInquiry.saved = false;
            this.createInquiry.submitted = false;
            this.createInquiry.modal = !this.createInquiry.modal;
        },
        insertUpdateInquiry() {
            if (
                !this.createInquiry.data.subject ||
                !this.createInquiry.data.inquiry ||
                !this.createInquiry.data.senderEmail ||
                this.createInquiry.data.subject.length <= 0 ||
                this.createInquiry.data.inquiry.length <= 0 ||
                this.createInquiry.data.senderEmail.length <= 0
            ) {
                this.$root.prompt('Please fill in all the required fields.');
                this.createInquiry.subjectEmpty = !this.createInquiry.data.subject || this.createInquiry.data.subject.length <= 0;
                this.createInquiry.inquiryEmpty = !this.createInquiry.data.inquiry || this.createInquiry.data.inquiry.length <= 0;
                this.createInquiry.senderemailEmpty = !this.createInquiry.data.senderEmail || this.createInquiry.data.senderEmail.length <= 0;
                this.createInquiry.saved = false;
                return;
            }
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/inquiry/save',
                data: this.createInquiry.data,
            }).then(response => {
                setTimeout(() => {
                    if (response.data.status === 1) {
                        this.createInquiry.submitted = true;
                        this.createInquiry.saved = true;
                    } else {
                        this.$root.prompt(response.data.text);
                    }
                }, this.createInquiry.done.delay);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.createInquiry.saved = false;
            });
        }
    },
    components: {
        navigation: navmenu,
    },
};
</script>
