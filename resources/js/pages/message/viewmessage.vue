<template #content>
    <div class="MessageView pb-5 w-[500px] h-[250px]">
        
        <div class="text-xl mb-2"><h1>Message from {{ message.sender_email }}</h1></div>
        <va-card
        class="h-full"
        stripe
        >
            <va-card-title>
                <pre class="flex align-self-lg-start text-sm font-sans">
Subject: {{ message.subject }}
Date: {{  formatMessageDate(message.created_at,) }}
                </pre>       
            </va-card-title>
            <va-card-content>
                <p class=" py-1 pt-2 px-1">{{ message.content }}</p>
            </va-card-content>
            
        </va-card>
    </div>
</template>

<script>
import formatDate from '@/functions/formatdate.js';

export default {
    props: {
        message: {
            type: Object,
            required: true,
        }
    },
    methods:{
        formatMessageDate(date) {
            return this.formatDate(date, 'MMMM DD, YYYY hh:mma', 'Invalid Date');
        },
        formatDate
    }
};
</script>
