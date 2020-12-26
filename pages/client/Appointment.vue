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
            <nb-icon name="check" :style="{ fontSize: 150, color: 'green' }" />
            <nb-text>Afspraak bewindvoerder EU</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>14:00 - 15:00</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>Jan 23 2020</nb-text>
              </nb-right>
          </nb-card-item>
          <image :source="require('../../assets/images/logo.png')" />
          </nb-body>
        </nb-card-item>
      </nb-card>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/Footer';
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
      }
    }
  }
</script>