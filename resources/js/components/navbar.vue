<template>
    <va-navbar
    color="primary"
    class="z-[5!important]"
    style="position: sticky; top:0px; padding: 0.5rem 1.2rem; box-shadow: 0 3px var(--va-background-primary); height: 62px;"
    >
        <template #left>
            <va-navbar-item 
            class="logo flex items-center"
            @click="$root.redirectToPage('/')"
            >
                <span class="ml-2">WHEELS</span>
            </va-navbar-item>
        </template>
        <template #right>
            <va-navbar-item
            v-if="!$root.auth.userType && !$root.auth.info"
            class="self-center"
            @click="$root.redirectToPage('/aboutus')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/aboutus')}"
            >
                <div class="px-3 py-[8px] rounded hover:bg-blue-600">
                    <va-icon />
                    <span class="align-middle select-none">About Us</span>
                </div>
            </va-navbar-item>
            <va-navbar-item
            v-if="!$root.auth.userType && !$root.auth.info"
            class="self-center"
            @click="$root.redirectToPage('/contactus')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/contactus')}"
            >
                <div class="px-3 py-[8px] rounded hover:bg-blue-600">
                    <va-icon />
                    <span class="align-middle select-none">Contact Us</span>
                </div>
            </va-navbar-item>
            <va-navbar-item
            v-if="!$root.auth.userType && !$root.auth.info"
            class="self-center"
            @click="$root.redirectToPage('/terms')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/terms')}"
            >
                <div class="px-3 py-[8px] rounded hover:bg-blue-600">
                    <va-icon />
                    <span class="align-middle select-none">Terms and Conditions</span>
                </div>
            </va-navbar-item>
            <va-navbar-item
            v-if="$root.auth && $root.auth.info"
            class="self-center"
            @click="$root.redirectToPage('/dashboard')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/dashboard')}"
            >
                <div class="px-3 py-[8px] rounded hover:bg-blue-600">
                    <va-icon />
                    <span class="align-middle select-none">Home</span>
                </div>
            </va-navbar-item>
            <!-- <va-navbar-item
            v-if="$root.auth && $root.auth.info && $root.auth.userType == 0"
            class="h-full self-center"
            @click="$root.redirectToPage('/enrollment')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/enrollment')}"
            >
                <div class="px-3 py-[8px] rounded hover:bg-blue-600">
                    <va-icon />
                    <span class="align-middle select-none">Enroll</span>
                </div>
            </va-navbar-item> -->
            <va-navbar-item
            v-if="$root.auth && $root.auth.info && $root.auth.userType == 0"
            class="h-full self-center"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/enrollment'), 'rounded bg-blue-600 active': $root.isActivePage('/enrollment/schedules'), }"
            >
                <va-dropdown
                class="align-middle"
                stickToEdges
                >
                    <template #anchor>
                        <div class="px-3 py-[8px] rounded hover:bg-blue-600 whitespace-nowrap">
                            <va-icon />
                            <span class="align-middle select-none">Service</span>
                        </div>
                    </template>

                    <va-dropdown-content class="z-[10!important]">
                        <div
                        class="select-none p-0.5 hover:text-blue-500"
                        @click="$root.redirectToPage('/enrollment/schedules')"
                        :class="{'text-blue-600 active': $root.isActivePage('/enrollment/schedules'), 'text-neutral-500': !$root.isActivePage('/enrollment/schedules')}"
                        >
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="date_range"
                                size="small"
                                />
                                Schedules
                            </p>
                        </div>
                        <va-divider />
                        <div
                        class="select-none p-0.5 hover:text-blue-500"
                        @click="$root.redirectToPage('/enrollment')"
                        :class="{'text-blue-600 active': $root.isActivePage('/enrollment'), 'text-neutral-500': !$root.isActivePage('/enrollment')}"
                        >
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="draw"
                                size="small"
                                />
                                Enrollment
                            </p>
                        </div>
                    </va-dropdown-content>
                </va-dropdown>
            </va-navbar-item>
            <va-navbar-item
            class="self-center"
            @click="$root.redirectToPage('/newsevent')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/newsevent')}"
            >
                <div class="px-3 py-[8px] rounded hover:bg-blue-600">
                    <va-icon />
                    <span class="align-middle select-none">News</span>
                </div>
            </va-navbar-item>
            <va-navbar-item
            v-if="$root.auth && $root.auth.info"
            class="self-center"
            >
            <va-dropdown
                class="align-middle"
                placement="bottom-right"
                stickToEdges
                >
                    <template #anchor>
                        <div class="hover:bg-blue-600 px-2.5 py-2 rounded" title="Notifications">
                            <va-badge
                            overlap
                            :dot="notifications.filter(notification => notification.read_at === null).length > 0"
                            color="#EF4444"
                            style="--va-badge-text-wrapper-border-radius: 40px;"
                            >
                                <va-icon
                                name="notifications"
                                />
                            </va-badge>
                        </div>
                    </template>
                    <va-dropdown-content>
                        <div
                        class="hover:text-blue-500 max-w-xs p-0.5 select-none text-neutral-500 text-right"
                        @click="readAllNotifications" 
                        >
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="done_all"
                                size="small"
                                />
                                Mark all as read
                            </p>
                        </div>
                        <va-divider />
                        <div
                        class="hover:text-blue-500 max-w-xs p-0.5 select-none text-neutral-500 text-right"
                        @click="$root.redirectToPage('/notifications')"    
                        hidden>
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="visibility"
                                size="small"
                                />
                                View all
                            </p>
                        </div>
                        <template v-for="(row, i) in notifications" :key="i">
                            <va-divider />
                            <div 
                                class="max-w-xs p-0.5 text-neutral-500 flex"
                                :style="{ backgroundColor: row.read_at == null ? '#D0E1D4' : '', 'font-weight': row.read_at == null ? 'bold' : 'normal' }"
                                @click="readSingleNotifications(row.id)"
                            >
                                <div>
                                    <p>
                                        <va-icon
                                        class="mr-1 mb-1"
                                        :name="row.icon"
                                        size="small"
                                        
                                        />
                                        {{ row.title }}<br/>
                                        <span class="text-xs">{{ row.description }}</span>
                                        <br/>
                                    </p>
                                </div>   
                            </div>
                        </template>
                        <template v-if="notifications.length == 0">
                            <va-divider />
                            <div class="max-w-xs p-0.5 text-neutral-500">
                                <p>
                                    <va-icon
                                    class="mr-1 mb-1"
                                    name="info"
                                    size="small"
                                    />
                                    No new notifications.<br/>
                                </p>
                            </div>
                        </template>
                    </va-dropdown-content>
                </va-dropdown>
            </va-navbar-item>
            <va-navbar-item
            v-if="$root.auth && $root.auth.info"
            class="self-center"
            >
            <va-dropdown
                class="align-middle"
                placement="bottom-right"
                stickToEdges
                >
                    <template #anchor>
                        <div class="hover:bg-blue-600 px-2.5 py-2 rounded" title="Message">
                            <va-badge
                            overlap
                            :dot="messages.length > 0 ? true : false"
                            color="#EF4444"
                            style="--va-badge-text-wrapper-border-radius: 40px;"
                            >
                                <va-icon
                                name="message"
                                />
                            </va-badge>
                        </div>
                    </template>
                    <va-dropdown-content>
                        <template v-for="(row, i) in messages" :key="i">
                            <va-divider />
                            <div class="max-w-xs p-0.5 text-neutral-500" @click="OpenMessageModal(row)">
                                <p>
                                    {{ row.sender_email }}<br/>
                                    <span class="text-xs">{{ row.subject }}</span>
                                </p>
                            </div>
                        </template>

                        <template v-if="messages.length == 0">
                            <div class="max-w-xs p-0.5 text-neutral-500">
                                <p>
                                    <va-icon
                                    class="mr-1 mb-1"
                                    name="info"
                                    size="small"
                                    />
                                    No messages.<br/>
                                </p>
                            </div>
                        </template>
                        <va-divider />
                        <div
                        class="hover:text-blue-500 max-w-xs p-0.5 select-none text-neutral-500 text-right"
                        @click="ComposeModal()">
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="visibility"
                                size="small"
                                />
                                Compose
                            </p>
                        </div>
                        <va-divider />
                        <div
                        class="hover:text-blue-500 max-w-xs p-0.5 select-none text-neutral-500 text-right"
                        @click="$root.redirectToPage('/Messages')
                        "
                        hidden>
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="visibility"
                                size="small"
                                />
                                View all Messages
                            </p>
                        </div>


                    </va-dropdown-content>
                </va-dropdown>
            </va-navbar-item>
            <va-navbar-item
            v-if="$root.auth && $root.auth.info"
            class="self-center"
            @click="$root.redirectToPage('/faq')"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/faq')}"
            >
                <div class="px-2.5 py-[8px] rounded hover:bg-blue-600" title="Help">
                    <va-icon
                    name="help"
                    />
                </div>
            </va-navbar-item>
            <va-navbar-item
            v-if="$root.auth && $root.auth.info"
            class="self-center"
            :class="{'rounded bg-blue-600 active': $root.isActivePage('/account')}"
            >
                <va-dropdown
                class="align-middle"
                stickToEdges
                >
                    <template #anchor>
                        <div class="px-2.5 py-[8px] rounded hover:bg-blue-600 whitespace-nowrap">
                            <va-icon
                            name="account_circle"
                            />
                            <span class="ml-2 align-middle select-none">{{ $root.auth.info.firstname }} {{ $root.auth.info.lastname.substring(0, 1).toUpperCase() + "." }}</span>
                        </div>
                    </template>

                    <va-dropdown-content class="z-[10!important]">
                        <div
                        class="select-none p-0.5 hover:text-blue-500"
                        @click="$root.redirectToPage('/account')"
                        :class="{'text-blue-600 active': $root.isActivePage('/account'), 'text-neutral-500': !$root.isActivePage('/account')}"
                        >
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="person_outline"
                                size="small"
                                />
                                Profile
                            </p>
                        </div>
                        <va-divider />
                        <div
                        class="text-neutral-500 select-none p-0.5 hover:text-blue-500"
                        @click="$root.redirectToPage('/logout')
                        ">
                            <p>
                                <va-icon
                                class="mr-1 mb-1"
                                name="logout"
                                size="small"
                                />
                                Logout
                            </p>
                        </div>
                    </va-dropdown-content>
                </va-dropdown>
            </va-navbar-item>
        </template>
    </va-navbar>

    <div
    v-if="$root.auth && $root.auth.userType === 0"
    id="inquiry-btn"
    class="fixed right-[2rem] bottom-[2rem] z-10"
    >
        <va-button
        title="Contact Us"
        icon="wysiwyg"
        size="large"
        round
        :disabled="createInquiry.data.student_id ? true : false"
        @click="
            createInquiry.data.student_id = $root.auth.info.user_id,
            createInquiry.modal = !createInquiry.modal
        "
        />
    </div>

    <va-modal
    v-model="createInquiry.modal"
    noOutsideDismiss
    noPadding
    >
        <template #content>
            <div class="w-[410px] p-5">
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
                        <a href="#" class="text-blue-700 hover:text-blue-500 hover:underline">{{ formatMobileNumber($root.config.contactNumberOne) }}</a>
                        or
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
                            @click="
                                createInquiry.data = { ...createInquiry.resetData },
                                createInquiry.subjectEmpty = false,
                                createInquiry.inquiryEmpty = false,
                                createInquiry.saved = false,
                                createInquiry.submitted = false,
                                createInquiry.modal = !createInquiry.modal
                            ">
                                <p class="font-normal">Cancel</p>
                            </va-button>
                        </div>
                        <div class="flex w-1/2 justify-between">
                            <va-button
                            icon="check"
                            :loading="createInquiry.saved"
                            :disabled="createInquiry.saved"
                            @click="createInquiry.saved = true, insertUpdateInquiry('create')"
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
                            @click="
                                createInquiry.data = { ...createInquiry.resetData },
                                createInquiry.subjectEmpty = false,
                                createInquiry.inquiryEmpty = false,
                                createInquiry.saved = false,
                                createInquiry.submitted = false,
                                createInquiry.modal = !createInquiry.modal
                            ">
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
        </template>
    </va-modal>

    <va-modal
    v-model="isMessageModalOpen" no-outside-dismiss close-button
    cancel-text=""
    ok-text=""
    >
        <template #content>
            <div
            class="contenthere">
                <template v-if="activeMessageModal === 'viewMessage'">
                    <viewMessage :message="message" />
                </template>
                <template v-if="activeMessageModal === 'replyMessage'">
                    <replyMessage
                    :senderId="message.sender_id"
                    :sender-email="message.sender_email"
                    :senderSubject="message.subject"
                    :senderContent="message.content"
                    :senderDate="message.created_at"
                    @close-modal="handleReplyModalClose()"
                    @message-sent="handleReplyModalClose()"
                    @message-cancelled="handleReplyModalClose()"
                    />
                </template>
            </div>
            <div class="flex w-full gap-x-3 mt-[10px]" :hidden="isReplyDeleteHidden">
                <div class="flex w-1/2 justify-between" >
                    <va-button
                    icon="reply"
                    @click="openReplyModal()"
                    >
                        <p class="font-normal">Reply</p>
                    </va-button>
                </div>
                <div class="flex w-1/2 justify-between">
                    <va-button
                    icon="delete"
                    preset="Danger"
                    :loading="isDeleteLoading"
                    @click="deleteMessage()"
                    >
                        <p class="font-normal">Delete</p>
                    </va-button>
                </div>
            </div>
        </template>
    </va-modal>

    <va-modal
    v-model="isComposeModalOpen"
    >
        <template #content>
            <composeMessage
            @message-sent="closeComposeModal()"
            @message-cancelled="closeComposeModal()"
            />
        </template>
    </va-modal>

</template>

<style scoped>
.va-navbar__right>.va-navbar__item {
    margin-right: 0.3em;
}
.va-navbar__right>.va-navbar__item .active,
.va-dropdown__content .active {
    pointer-events: none;
}
/* #inquiry-btn .va-button {
    box-shadow: 0 3px #CCCCCC!important;
} */
</style>

<script>
import formatDate from '@/functions/formatdate.js';
import viewMessage from '@/pages/message/viewmessage.vue';
import replyMessage from '@/pages/message/replymessage.vue';
import composeMessage from '@/pages/message/composemessage.vue';

const newInq = {
    subject: "",
    inquiry: null,
    student_id: null,
};

export default {
    components: {
        viewMessage,
        replyMessage,
        composeMessage
    },
    data () {
        return {
            createInquiry: {
                modal: false,
                subjectEmpty: false,
                inquiryEmpty: false,
                submitted: false,
                saved: false,
                done: {
                    delay: 500,
                },
                resetData: { ...newInq },
                data: { ...newInq }
            },
            isNotificationsLoading: false,
            notifications: [],
            isMessagingLoading: false,
            isMessageModalOpen: false,
            isReplyModalOpen: false,
            isDeleteLoading: false,
            isReplyDeleteHidden: false,
            notifications: [],
            notification: {
                id: null
            },
            messages: [],
            message: [],
            activeMessageModal: '',
            isComposeModalOpen: false
        }
    },
    mounted () {
        const disallowedPaths = ['/', '/terms', '/aboutus', '/contactus','/newsevent'];

        if (!disallowedPaths.includes(window.location.pathname)) {
            this.getNotifications();
            this.getMessages();
        }
    },
    methods: {
        formatMobileNumber(mobile) {
            const mobileWithoutLeadingZero = mobile.substring(1);
            return `(` + this.$root.config.contactCountryCode + `) ${mobileWithoutLeadingZero.slice(0, 3)} ${mobileWithoutLeadingZero.slice(3, 6)} ${mobileWithoutLeadingZero.slice(6)}`;
        },
        insertUpdateInquiry(method) {
            if (method === 'create') {
                axios({
                    method: 'POST',
                    type: 'JSON',
                    url: '/inquiry/save',
                    data: this.createInquiry.data
                }).then(response => {
                    setTimeout(() => {
                        if (response.data.status == 1) {
                            // this.$root.prompt(response.data.text);
                            method === 'create' && (
                                // this.createInquiry.data = { ...newInq },
                                // this.createInquiry.modal = false,
                                // this.createInquiry.subjectEmpty = false,
                                // this.createInquiry.inquiryEmpty = false,
                                // this.createInquiry.saved = false,
                                this.createInquiry.submitted = true
                            );
                        } else this.$root.prompt(response.data.text);
                    }, this.createInquiry.done.delay);
                }).catch(error => {
                    // this.$root.prompt(error.response.data.message);
                    let resDataError = Object.keys(error.response.data.errors);

                    if (resDataError.filter(key => key == 'subject').length) {
                        method === 'create' && (this.createInquiry.subjectEmpty = true);
                    }
                    if (resDataError.filter(key => key == 'inquiry').length) {
                        method === 'create' && (this.createInquiry.inquiryEmpty = true);
                    }

                    method === 'create' && (this.createInquiry.saved = false);
                });
            } else this.$root.prompt();
        },
        getNotifications() {
            this.isNotificationsLoading = true;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/notifications/get',
                data: { limit: this.$root.config.notifLimit }
            }).then(response => {
                if (response.data.status == 1) {
                    this.notifications = response.data.result;
                } else this.$root.prompt(response.data.text);
                this.isNotificationsLoading = false;
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.isNotificationsLoading = false;
            });
        },
        readAllNotifications() {
            this.isNotificationsLoading = true;
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/notifications/read',
            }).then(response => {
                if (response.data.status == 1) {
                    this.getNotifications();
                } else this.$root.prompt(response.data.text);
                this.isNotificationsLoading = false;
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.isNotificationsLoading = false;
            });
        },
        getMessages() {
            this.isMessagingLoading = true;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/messages/get',
            }).then(response => {
                if (response.data.status == 1) this.messages = response.data.result;
                else this.$root.prompt(response.data.text);
                this.isMessagingLoading = false;
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.isMessagingLoading = false;
            });
        },
        OpenMessageModal(row) {
            this.message = row;
            this.isMessageModalOpen = true;
            this.activeMessageModal = 'viewMessage';
            this.isReplyDeleteHidden = false;
        },
        openReplyModal() {
            this.activeMessageModal = 'replyMessage';
            this.isReplyDeleteHidden = true;
        },
        handleReplyModalClose() {
            this.isReplyDeleteHidden = false;
            this.isReplyModalOpen = false;
            this.isMessageModalOpen = true;
            this.activeMessageModal = "viewMessage"
        },
        cancelReply() {
            this.isReplyDeleteHidden = false;
            this.isReplyModalOpen = false;
            this.isMessageModalOpen = true;
            this.activeMessageModal = "viewMessage";
        },
        deleteMessage() {
            this.isDeleteLoading = true;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/messages/delete',
                data: { id: this.message.id }
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.message = {};
                    this.isDeleteLoading = false;
                    this.isMessageModalOpen = false;
                    this.getMessages();
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.isDeleteLoading = false;
            });
        },
        ComposeModal() {
            this.isComposeModalOpen = true;
        },
        closeComposeModal() {
            this.isComposeModalOpen = false;
        },
        readSingleNotifications(id) {
            this.notification.id = id;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/notifications/single_read',
                data: { id: this.notification.id },
            }).then(response => {
                if (response.data.status == 1) {
                    this.getNotifications();
                } else this.$root.prompt(response.data.text);
                
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
              
            });
        },
        formatDate
    }
}
</script>
