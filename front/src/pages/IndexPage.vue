<template>
  <div class="q-pa-md row justify-center">
    <div style="width: 100%">
      <q-chat-message
        v-for="c in store.chats"
        :key="c.id"
        :text="[c.message]"
        :sent="c.userEnviado_id==1"
      />
    </div>
  </div>
</template>

<script>
import {useCounterStore} from "stores/example-store";

export default {
  data(){
    return{
      store:useCounterStore()
    }
  },
  created() {

  },
  methods:{
    click(){
      console.log(this.store.chats)
    }
  }
}
</script>
