<template>
  <nb-container>
    <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>{{ $root.lang.t('my_account') }}</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-card>
        <nb-card-item header bordered :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
          <nb-text class="text-account header" v-if="!editProfile">{{userData.profileData.Voornamen}} {{userData.profileData.Achternaam}}</nb-text>
          <nb-item floatingLabel v-if="editProfile">
            <nb-label>{{ $root.lang.t('name') }}</nb-label>
            <nb-input value="Mark van Huizen" />
          </nb-item>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-card-item v-if="!editProfile">
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="person"></nb-icon>
                <nb-text class="text-account">{{ $root.lang.t('BSN') }}: {{userData.profileData.BSN}}</nb-text>
              </nb-left>           
            </nb-card-item>
            <nb-card-item v-if="!editProfile">
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="map"></nb-icon>
                <nb-text class="text-account">{{userData.profileData.Adres}}{{ "\n" }}{{userData.profileData.Postcode}} {{userData.profileData.Woonplaats}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item v-if="!editProfile">
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="mail"></nb-icon>
                <nb-text class="text-account">{{userData.email}}</nb-text>
              </nb-left>    
            </nb-card-item>    
            <nb-card-item v-if="!editProfile">
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="call"></nb-icon>
                <nb-text class="text-account">{{userData.profileData.Telefoon_Mob}}</nb-text>
              </nb-left>           
            </nb-card-item>
            <nb-item  floatingLabel v-if="editProfile">
              <nb-label>Phone number</nb-label>
              <nb-input value="+0200300500" placeholder="Phone number" />
            </nb-item>
          </nb-body>
        </nb-card-item>
        <nb-card-item footer>
        </nb-card-item>
      </nb-card>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="account"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../included/Footer';
  import store from '../store';
  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav },
    data() {
      return {
        editProfile: false
      };
    },
      computed: {
    userData () {
      return store.state.userObj;
    }
  },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      clickEditProfile: function () {
        this.editProfile = !this.editProfile
      },
      goToPage: function (page) {
        this.navigation.navigate(page);
      }
    }
  }
</script>
<style>

.text-account {
  color:#0078ae;
}
.header {
  font-weight: bold;
  font-size: 24px;
}
</style>