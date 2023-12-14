<template>
    <div>
      <navigation />
      <div class="m-5">
        <div>
          <h1 class="text-2xl">Frequently Asked Questions</h1>
        </div>
        <div>
          <h2 class="text-xl">All about the Driving School</h2>
        </div>
      </div>
      <div class="h-screen m-5">
        <va-accordion class="w-12/12 text-sm" multiple>
          <va-collapse
            class="w-12/12 pb-3"
            v-for="(item, idx) in items"
            :key="idx"
            :header="item.question"
            icon="help"
            :v-model="idx < 5 ? true : false"
            color="BackgroundPrimary"
          >
            <div>
              <div>
                <va-card stripe>
                  
                  <va-card-content>
                    <p>{{ item.answer }}</p>
                  </va-card-content>
                </va-card>
              </div>
            </div>
          </va-collapse>
        </va-accordion>
      </div>
    </div>
  </template>
  
  
  
  
  <script>
  import navmenu from '@/components/navbar.vue';
  import formatDate from '@/functions/formatdate.js';
  
  const description = 'ewewewqeqwe'
  
  export default {
      components: {
          navigation: navmenu
      },
      data(){
          return{
              UpdatedDate: '',
              items: [],
              value: true
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
                  url: '/faqs'
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
          formatDate
      }
  
  }
  
  </script>
  
  <style scoped>
  .va-collapse + .va-collapse {
    border-top: 1px solid var(--va-background-element);
  }
  </style>
  