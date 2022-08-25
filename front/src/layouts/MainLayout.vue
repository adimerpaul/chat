<template>
  <div class="WAL position-relative bg-grey-4" :style="style">
    <q-layout view="lHh Lpr lFf" class="WAL__layout shadow-3" container>
      <q-header elevated>
        <q-toolbar class="bg-grey-3 text-black">
          <q-btn
            round
            flat
            icon="keyboard_arrow_left"
            @click="toggleLeftDrawer"
          />

          <q-btn round flat>
            <q-avatar>
              <img v-if="user.avatar" :src="user.avatar">
            </q-avatar>
          </q-btn>

          <span class="q-subtitle-1 q-pl-md">
            {{ user.person }}
          </span>

          <q-space/>

          <q-btn round flat icon="search" />
          <q-btn round flat>
            <q-icon name="attachment" class="rotate-135" />
          </q-btn>
          <q-btn round flat icon="more_vert">
            <q-menu auto-close :offset="[110, 0]">
              <q-list style="min-width: 150px">
                <q-item clickable>
                  <q-item-section>Contact data</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Block</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Select messages</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Silence</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Clear messages</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Erase messages</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </q-toolbar>
      </q-header>

      <q-drawer
        v-model="leftDrawerOpen"
        show-if-above
        bordered
        :breakpoint="690"
      >
        <q-toolbar class="bg-grey-3">
          <q-avatar class="cursor-pointer">
            <img :src="url+'/../'+'bart.jpg'" />
          </q-avatar>

          <q-space />

          <q-btn round flat icon="message" />
          <q-btn round flat icon="more_vert">
            <q-menu auto-close :offset="[110, 8]">
              <q-list style="min-width: 150px">
                <q-item clickable>
                  <q-item-section>New group</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Profile</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Archived</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Favorites</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Settings</q-item-section>
                </q-item>
                <q-item clickable>
                  <q-item-section>Logout</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>

          <q-btn
            round
            flat
            icon="close"
            class="WAL__drawer-close"
            @click="toggleLeftDrawer"
          />
        </q-toolbar>

        <q-toolbar class="bg-grey-2">
          <q-input rounded outlined dense class="WAL__field full-width" bg-color="white" v-model="search" placeholder="Search or start a new conversation">
            <template v-slot:prepend>
              <q-icon name="search" />
            </template>
          </q-input>
        </q-toolbar>

        <q-scroll-area style="height: calc(100% - 100px)">
          <q-list>
            <q-item
              v-for="(conversation, index) in users"
              :key="conversation.id"
              clickable
              v-ripple
              @click="setCurrentConversation(index)"
            >
              <q-item-section avatar>
                <q-avatar>
                  <img :src="conversation.avatar">
                </q-avatar>
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">
                  {{ conversation.person }}
                </q-item-label>
                <q-item-label class="conversation__summary" caption>
                  <q-icon name="check" v-if="conversation.sent" />
                  <q-icon name="not_interested" v-if="conversation.deleted" />
                  {{ conversation.caption }}
                </q-item-label>
              </q-item-section>

              <q-item-section side>
                <q-item-label caption>
                  {{ conversation.time }}
                </q-item-label>
                <q-icon name="keyboard_arrow_down" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-scroll-area>
      </q-drawer>

      <q-page-container class="bg-grey-2">
        <router-view />
      </q-page-container>

      <q-footer>
        <q-toolbar class="bg-grey-3 text-black row">
<!--          <q-form @submit.prevent="chatInsert">-->
            <q-btn round flat icon="insert_emoticon" class="q-mr-sm" />
            <q-input @keyup.enter="chatInsert" rounded outlined dense class="WAL__field col-grow q-mr-sm" bg-color="white" v-model="message" placeholder="Type a message" />
            <q-btn round flat icon="mic" :loading="loading" />
<!--          </q-form>-->
        </q-toolbar>
      </q-footer>
    </q-layout>
  </div>
</template>

<script>
import {useCounterStore} from "stores/example-store";
import {date} from "quasar";
import io from "socket.io-client";
export default {
  name: 'WhatsappLayout',
  data(){
    return{
      loading:false,
      store:useCounterStore(),
      leftDrawerOpen:false,
      search:'',
      message:'',
      user:{},
      users :[],
      url:process.env.API
    }
  },
  created() {
    this.socketInstance = io("http://192.168.1.124:3000");
    this.socketInstance.on(
      "message:received", (data) => {
        this.store.chats.push(data);
      }
    )

    if (this.store.users.length == 0) {
      this.$api.get('user').then(res=>{
        res.data.forEach(r=>{
          r.person=r.name,
          r.avatar=this.url+'/../'+r.foto,
          r.caption='I\'m working on Quasar!',
          r.time='17:00',
          r.sent=true
        })
        this.users=res.data
        if (this.users.length>0){
          this.user = this.users[0]
          this.chatGet()
        }
      })
    }
  },
  methods:{
    chatGet(){
      this.loading=true
      this.$api.get('user/'+this.user.id).then(res=>{
        this.store.chats=res.data
        this.loading=false
      })
    },
    chatInsert(){
      let chat={
        fecha:date.formatDate(new Date(),'YYYY-MM-DD HH:mm:ss'),
        message:this.message,
        userEnviado_id:1,
        userRecibido_id:this.user.id,
      }
      this.store.chats.push(chat)
      this.socketInstance.emit('message', chat);
      this.$api.post('chat',{
        userEnviado_id:1,
        message:this.message,
        userRecibido_id:this.user.id,
      })
      this.message=''
    },
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    setCurrentConversation (index) {
      this.user=this.users[index]
      this.store.chats=[]
      this.$api.get('user/'+this.user.id).then(res=>{
        this.store.chats=res.data
      })
    }
  },
  computed:{
    style () {
      return {height: this.$q.screen.height + 'px'}
    }
  }
  // setup () {
  //   const $q = useQuasar()
  //   const leftDrawerOpen = ref(false)
  //   const search = ref('')
  //   const message = ref('')
  //   const userIndex = ref(0)
  //   const user = computed(() => {
  //     return users[ userIndex.value ]
  //   })
  //   const style = computed(() => ({
  //     height: $q.screen.height + 'px'
  //   }))
  //   function toggleLeftDrawer () {
  //     leftDrawerOpen.value = !leftDrawerOpen.value
  //   }
  //   function setCurrentConversation (index) {
  //     userIndex.value = index
  //   }
  //   return {
  //     leftDrawerOpen,
  //     search,
  //     message,
  //     userIndex,
  //     users,
  //     user,
  //     setCurrentConversation,
  //     style,
  //     toggleLeftDrawer
  //   }
  // }
}
</script>

<style lang="sass">
.WAL
  width: 100%
  height: 100%
  padding-top: 20px
  padding-bottom: 20px
  &:before
    content: ''
    height: 127px
    position: fixed
    top: 0
    width: 100%
    background-color: #009688
  &__layout
    margin: 0 auto
    z-index: 4000
    height: 100%
    width: 90%
    max-width: 950px
    border-radius: 5px
  &__field.q-field--outlined .q-field__control:before
    border: none
  .q-drawer--standard
    .WAL__drawer-close
      display: none
@media (max-width: 850px)
  .WAL
    padding: 0
    &__layout
      width: 100%
      border-radius: 0
@media (min-width: 691px)
  .WAL
    &__drawer-open
      display: none
.conversation__summary
  margin-top: 4px
.conversation__more
  margin-top: 0!important
  font-size: 1.4rem
</style>
