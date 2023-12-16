<template>
    <navigation />
    <div class="flex flex-col col-span-1 m-5 flex-center">
      <div class="heading">
        <h1 class="text-2xl">News</h1>
      </div>
      <div class="body mt-5 mr-5 w-fit lg:w-[700px] sm:w-[300px]" style="background-color: whitesmoke;">
        <va-accordion class="w-12/12">
          <div
            v-for="(item, idx) in items"
            
            text-color="textPrimary"
            color="textInverted"
            flat
            class="pb-3"
          >
            <div class="flex flex-col flex-wrap gap-5">
              <VaCard stripe primary>
                <VaCardBlock
                  class="flex-nowrap"
                  horizontal
                >
                  <div class="flex-auto">
                    <VaCardTitle>
                      <h1>
                        {{ item.title }}
                        <br />
                        Date created: {{ formatDate(item.created_at, 'MMMM DD, YYYY', 'N/A') }}
                      </h1>
                    </VaCardTitle>
                    <VaCardContent>
                      <p style="word-wrap: break-word;">{{ item.description }}</p>
                    </VaCardContent>
                  </div>
                  <va-image
                  class="
                      min-w-[150px] max-w-[150px!important]
                      min-h-[150px] max-h-[150px!important]
                      rounded-sm bg-neutral-100
                      flex-grow-0 flex-shrink-0 basis-52
                  "
                  :src="$root.forgeImageFile(item.image[0], 'news', false)"
                  :placeholder-src="$root.forgeImageFile(null, null, false)"
                  lazy
                  />
                </VaCardBlock>
              </VaCard>
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
          method: 'POST',
          type: 'JSON',
          url: '/news/active',
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
  