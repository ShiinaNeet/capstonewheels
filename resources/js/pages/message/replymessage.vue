<template #content>
    <div class="">
       <div class="text-xl mb-2">
            <h1 class="text-2xl pb-2">Reply</h1>
           <!--  <h1>Send to: {{ senderEmail }}</h1> -->
            <!--<h1>Send to: {{ senderId }}</h1> -->
          <!-- <h1>From   : {{ $root.auth.email }}</h1>-->
       </div>
       <div class="flex flex-col w-[500px]">
            <div class="flex flex-col ">
                
                <div class="PrevConvo w-full pb-3">
                    <div class="FromMessage">
                        <va-card
                        color="primary"
                        gradient
                        >
                            <va-card-title>{{ senderName }}<br/> {{ senderEmail }}</va-card-title>
                            <va-card-content>
                               <p class="font-bold"> Subject: {{  message.subject}}</p>
                               <p class="font-bold"> Date: {{ formatMessageDate(senderDate) }}</p>
                                <br />
                                {{   senderContent }}
                            </va-card-content>
                        </va-card>
                    </div>
                </div>
                <va-input
                v-model="message.content"
                type="textarea"
                placeholder="Content here"
                label="Content"
                inner-label
                maxlength="255"
                :min-rows="10"
                :max-rows="10"
                class="flex w-full"
                />
            </div>
            <div class="flex w-full gap-x-3 mt-[15px]">
                <div class="flex w-1/2 justify-between">
                    <va-button
                    preset="secondary"
                    @click="cancelReply()"
                    >
                        <p class="font-normal">Cancel</p>
                    </va-button>
                </div>
                <div class="flex w-1/2 justify-between">
                    <va-button
                    icon="save"
                    :loading="isMessagingLoading"
                    @click="sendMessage()"
                    >
                        <p class="font-normal">Send</p>
                    </va-button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import viewMessage from '@/pages/message/viewmessage.vue';
import formatDate from '@/functions/formatdate.js';
export default {
    data() {
        return {
            isMessagingLoading: false,
            message: {
                recipient_id: this.senderId,
                subject: this.senderSubject,
                content: '',
                name: this.senderName,
                saved: false
            }
        };
    },
    props: {
        senderId: {
            type: Number,
            required: true,
        },
        senderEmail: {
            type: String,
            required: true
        },
        senderSubject: {
            type: String,
            required: true
        },
        senderContent: {
            type: String,
            required: true
        },
        senderDate: {
            type: String,
            required: true
        },
        senderName: {
            type: String,
            required: true
        }
    },
    methods: {
        sendMessage() {
            this.isMessagingLoading =true;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/messages/send_reply',
                data: this.message
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.isMessagingLoading = false;
                    this.message = {};
                    this.$emit('message-sent');
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                let resDataError = Object.keys(error.response.data.errors);
                console.log(resDataError);
                this.isMessagingLoading = false;
            });
        },
        cancelReply() {
            this.$emit('message-cancelled');
        },
        formatMessageDate(date) {
            return this.formatDate(date, 'MMMM DD, YYYY hh:mma', 'Invalid Date');
        },
        formatDate
    },
    components: {
        viewMessage
    }
};
</script>
