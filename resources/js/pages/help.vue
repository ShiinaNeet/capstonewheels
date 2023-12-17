<template>
    <div>
        <navigation />
        <div 
        class="m-5 flex"
        v-if="activeWindow === 'parent'"
        >
            
            <div class="grid grid-cols-1 place-items-center ">
                <div class="w-1/2">
                    <h1 class="text-2xl">Help</h1>
                </div>
                <div class="w-1/2">
                    <h2 class="text-2xl pb-3">Click to know all about the Driving School!</h2>
                </div>
                <div class="w-1/2 p-2"
                @click="activeWindow = 'Enrollment'"
                >
                    <VaCard
                    color="primary"
                    gradient
                    >
                        <VaCardTitle ><p class="text-2xl">Enrollment</p> </VaCardTitle>
                        <VaCardContent>
                            Know all about Enrollment!
                        </VaCardContent>
                    </VaCard>
                </div>
                <div class="w-1/2 p-2"
                @click="activeWindow = 'Services'"
                >
                    <VaCard
                    color="primary"
                    gradient
                    >
                        <VaCardTitle ><p class="text-2xl">Services</p> </VaCardTitle>
                        <VaCardContent>
                            Know all about Services!
                        </VaCardContent>
                    </VaCard>
                </div>
                <div class="w-1/2 p-2"
                @click="activeWindow = 'Payment'"
                >
                    <VaCard
                    color="primary"
                    gradient
                    >
                        <VaCardTitle ><p class="text-2xl">Payment</p> </VaCardTitle>
                        <VaCardContent>
                            Know all about Payment!
                        </VaCardContent>
                    </VaCard>
                </div>
                <div class="w-1/2 p-2"
                @click="activeWindow = 'Schedule'"
                >
                    <VaCard
                    color="primary"
                    gradient
                    >
                        <VaCardTitle ><p class="text-2xl">Schedules</p> </VaCardTitle>
                        <VaCardContent>
                            Know all about Schedules!
                        </VaCardContent>
                    </VaCard>
                </div>
                <div class="w-1/2 p-2"
                @click="activeWindow = 'Others'"
                >
                    <VaCard
                    color="primary"
                    gradient
                    >
                        <VaCardTitle ><p class="text-2xl">Others</p> </VaCardTitle>
                        <VaCardContent>
                            Others
                        </VaCardContent>
                    </VaCard>
                </div>
            </div>
        </div>
        <div class="m-5 flex">
            <div 
            class="grid grid-cols-1 place-items-center" 
            v-if="activeWindow === 'Enrollment'">
                <div class="w-1/2 p-2">
                    <VaButton
                    icon="arrow_back"
                    color="blue"
                    class="mr-3 mb-5"
                    v-if="activeWindow !== 'parent'"
                    @click="setActiveWindow('parent')"
                    >
                    Go back
                    </VaButton>
                    <h1>Enrollment</h1>
                    <div v-for="item in filteredItems('Enrollment')"
                     :key="item.id" class="py-2 border-blue-600"
                    >
                        <VaCard
                        stripe
                        stripe-color="Focus"
                        gradient
                        outlined
                        >
                            <VaCardTitle>{{ item.title }}</VaCardTitle>
                            <VaCardContent>
                            {{ item.description }}
                            </VaCardContent>
                        </VaCard>
                    </div>  
                </div>
            </div>
            <div 
            class="grid grid-cols-1 place-items-center" 
            v-if="activeWindow === 'Services'">
                <div class="w-1/2 p-2">
                    <VaButton
                    icon="arrow_back"
                    color="blue"
                    class="mr-3 mb-2"
                    v-if="activeWindow !== 'parent'"
                    @click="setActiveWindow('parent')"
                    >
                    Go back
                    </VaButton>
                    <h1>Services</h1>
                    <div v-for="item in filteredItems('Service')"
                     :key="item.id" class="py-2 border-blue-600"
                    >
                        <VaCard
                        stripe
                        stripe-color="Focus"
                        gradient
                        outlined
                        >
                            <VaCardTitle>{{ item.title }}</VaCardTitle>
                            <VaCardContent>
                            {{ item.description }}
                            </VaCardContent>
                        </VaCard>
                    </div>  
                </div>
            </div>
            <div 
            class="grid grid-cols-1 place-items-center" 
            v-if="activeWindow === 'Payment'">
                <div class="w-1/2 p-2">
                    <VaButton
                    icon="arrow_back"
                    color="blue"
                    class="mr-3 mb-2"
                    v-if="activeWindow !== 'parent'"
                    @click="setActiveWindow('parent')"
                    >
                    Go back
                    </VaButton>
                    <h1>Payment</h1>
                    <div v-for="item in filteredItems('Payment')"
                     :key="item.id" class="py-2 border-blue-600"
                    >
                        <VaCard
                        stripe
                        stripe-color="Focus"
                        gradient
                        outlined
                        >
                            <VaCardTitle>{{ item.title }}</VaCardTitle>
                            <VaCardContent>
                            {{ item.description }}
                            </VaCardContent>
                        </VaCard>
                    </div>  
                </div>
            </div>
           
            <div 
            class="grid grid-cols-1 place-items-center" 
            v-if="activeWindow === 'Schedule'">
                <div class="w-1/2 p-2">
                    <VaButton
                    icon="arrow_back"
                    color="blue"
                    class="mr-3 mb-2"
                    v-if="activeWindow !== 'parent'"
                    @click="setActiveWindow('parent')"
                    >
                    Go back
                    </VaButton>
                    <h1>Schedule</h1>
                    <div v-for="item in filteredItems('Schedule')"
                     :key="item.id" class="py-2 border-blue-600"
                    >
                        <VaCard
                        stripe
                        stripe-color="Focus"
                        gradient
                        outlined
                        >
                            <VaCardTitle>{{ item.title }}</VaCardTitle>
                            <VaCardContent>
                            {{ item.description }}
                            </VaCardContent>
                        </VaCard>
                    </div>  
                </div>
            </div>
            <div 
            class="grid grid-cols-1 place-items-center" 
            v-if="activeWindow === 'Others'">
                <div class="w-1/2 p-2">
                    <VaButton
                    icon="arrow_back"
                    color="blue"
                    class="mr-3 mb-2"
                    v-if="activeWindow !== 'parent'"
                    @click="setActiveWindow('parent')"
                    >
                    Go back
                    </VaButton>
                    <h1>Others</h1>
                    <div v-for="item in filteredItems('Others')"
                     :key="item.id" class="py-2 border-blue-600"
                    >
                        <VaCard
                        stripe
                        stripe-color="Focus"
                        gradient
                        outlined
                        >
                            <VaCardTitle>{{ item.title }}</VaCardTitle>
                            <VaCardContent>
                            {{ item.description }}
                            </VaCardContent>
                        </VaCard>
                    </div>  
                </div>
            </div>
        </div>
        
    </div>
  </template>
  
  
  
  
  <script>
  import navmenu from '@/components/navbar.vue';
  import formatDate from '@/functions/formatdate.js';
  
  
  export default {
      components: {
          navigation: navmenu
      },
      data(){
          return{
              UpdatedDate: '',
              items: [],
              value: true,
              activeWindow: 'parent',
          }
      },
      mounted () {
          this.GetItems();
      },
      methods:{
          GetItems(){
              axios({
                  method: 'GET',
                  type: 'JSON',
                  url: '/help/get'
              })
              .then(response=> {
                  if(response.data.status == 1) {
                      this.items = response.data.result;
                  }
                  else{
                      this.$root.prompt(response.data.text);
                  }
              }).catch(error => {
                  this.$root.prompt(error.message);
              });
          },
          setActiveWindow(window) {
            this.activeWindow = window;
        },
          formatDate
      },
      computed: {
        filteredItems() {
            return (tag) => {
                return this.items.filter(item => item.tags === tag);
            };
        },
    }
  }
  
  </script>
  
  <style scoped>
  .va-collapse + .va-collapse {
    border-top: 1px solid var(--va-background-element);
  }
  </style>
  