<template>
    <div>
        <div class="flex flex-col w-[500px] h-fit">
            <div>
                <h1 class="pb-2 text-lg">Send to</h1>
                <va-select
                v-model="message.recipient_id"
                class="mb-2 w-full"
                label="Driving School Staffs"
                :options="staff.result"
                outline
                text-by="email"
                value-by="id"
                />
            </div>
            <div class="subject my-2">
                <va-card>
                    <va-content>
                        <va-input
                        v-model="message.subject"
                        placeholder="Subject here"
                        label="Subject"
                        inner-label
                        class="flex w-full my-2"

                        />
                    </va-content>
                </va-card>
            </div>
            <div class="content">
                <va-card>
                    <va-content>
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
                    </va-content>
                </va-card>
            </div>

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
                :loading="isComposeLoading"
                @click="sendMessage()"
                >
                    <p class="font-normal">Send</p>
                </va-button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isComposeLoading: false,
            option: {},
            staff: {
                status: 1,
                title: "Success!",
                text: "Success.",
                result: []
            },
            message: {
                sender_id: this.$root.auth.info.user_id,
                recipient_id: null,
                subject: '',
                content: '',
                saved: false
            }
        };
    },
    mounted() {
        this.getStaff();
    },
    methods: {
        sendMessage() {
            this.isComposeLoading =true;
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/messages/send_message',
                data: this.message
            }).then(response => {
                if (response.data.status == 1) {
                    this.$root.prompt(response.data.text);
                    this.isComposeLoading = false;
                    this.message = {};
                    this.$emit('message-sent');
                } else this.$root.prompt(response.data.text);
            }).catch(error => {
                let resDataError = Object.keys(error.response.data.errors);
                console.log(resDataError);
                this.isComposeLoading = false;
            });
        },
        cancelReply() {
            this.$emit('message-sent');
        },
        getStaff() {
            this.isComposeLoading = true;
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/staffs'
            }).then(response => {
                if (response.data.status == 1) {
                    this.staff.result = response.data.result;
                    console.log(this.staff.result);
                    this.message.sender_id = this.$root.auth.info.user_id;
                } else this.$root.prompt(response.data.text);
                this.isComposeLoading = false;
            }).catch(error => {
                this.$root.prompt(error.response.data.message);
                this.isComposeLoading = false;
            });
        }
    },
};
</script>
