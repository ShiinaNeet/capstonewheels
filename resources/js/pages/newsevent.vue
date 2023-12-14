<template>
    <navigation />
    <div class="flex-auto m-5">
      <div class="heading">
        <h1 class="text-2xl">News</h1>
      </div>
      <div class="body my-5 mr-5" style="background-color: whitesmoke;">
        <va-accordion class="w-12/12">
          <div
            v-for="(item, idx) in items"
            
            text-color="textPrimary"
            color="textInverted"
            flat
            class="pb-3"
          >
            <div class="">
              <va-card>
                <va-card-title>
                  <h1>
                   <strong class="text-sm"> {{ item.title }} </strong> <br />
                    Date created: {{ formatDate(item.created_at, 'MMMM DD, YYYY', 'N/A') }}
                  </h1>
                </va-card-title>
                <va-card-content class="text-base">
                  <p style="word-wrap: break-word;">{{ item.description }}</p>
                </va-card-content>
              </va-card>
            </div>
          </div>
        </va-accordion>
      </div>
    </div>
  </template>
  
  <script>
  import navmenu from '@/components/navbar.vue';
  import formatDate from '@/functions/formatdate.js';
  
  export default {
    components: {
      navigation: navmenu,
    },
    data() {
      return {
          value: true,
          items: []
      };
    },
    mounted() {
      this.GetItems();
    },
    methods: {
      GetItems() {
        axios({
          method: 'GET',
          type: 'JSON',
          url: '/news',
        })
          .then((response) => {
            if (response.data.status == 1) {
              this.items = response.data.result;
            } else {
              this.$root.prompt(response.data.text);
            }
          })
          .catch((error) => {
            this.$root.prompt(error.message);
          });
      },
      formatDate,
    },
  };
  </script>
  
  <style scoped>
  .va-collapse + .va-collapse {
    border-top: 1px solid var(--va-background-element);
  }
  </style>
  