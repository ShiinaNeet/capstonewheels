<template #content>
    <div class="MessageView pb-5 w-[500px] h-[250px]">
        
        <div class="text-xl mb-2"><h1>Message from {{ message.sender_email }}</h1></div>
        <div class="text-xl mb-2"></div>
        <va-card
        class="h-full"
        stripe
        >
            <va-card-title><h2>Subject: {{ message.subject }}</h2></va-card-title>
            <va-card-content>
                <p class=" py-1 pt-2 px-1">{{ message.content }}</p>
            </va-card-content>
            
        </va-card>
    </div>
</template>

<script>
export default {
    props: {
        message: {
            type: Object,
            required: true,
        }
    }
};
</script>
