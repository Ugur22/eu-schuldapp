<template>
  <nb-container>
    <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Uw afspraak</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
        <nb-h1>Uw afspraak is gemaak</nb-h1>
      </view>
      <nb-card>
        <nb-card-item>
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-icon name="checkmark" :style="{ fontSize: 150, color: 'green' }" />
            <nb-text>{{navigation.getParam('title')}}</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>{{navigation.getParam('time')}}</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>{{navigation.getParam('date')}}</nb-text>
              </nb-right>
          </nb-card-item>
          <image :source="require('../../assets/images/logo.png')" />
          </nb-body>
        </nb-card-item>
      </nb-card>
    <nb-button full info class="btns" :on-press="() => goToPage('AppointmentsConsultant')">
      <nb-icon name="arrow-back" :on-press="goBack" />
      <nb-text class="text-btn">Terug naar alle afspraken</nb-text>
    </nb-button>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/FooterConsultant';
  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav },
    data() {
      return {
        conslutant: 'Erik Jansen'
      };
    },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      goToPage: function (page) {
      this.navigation.navigate(page);
    },
    }
  }
</script>
<style>
.btns {
  padding:10px;
  background-color:#0078ae;
  margin:10px;
  align-items: center;
  border-radius: 10px;
  justify-content: center;
}

.text-btn {
  font-weight: bold;
}
</style>