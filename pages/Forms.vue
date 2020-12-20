<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Formulieren</nb-title>
      </nb-body>
      <nb-right>
        <nb-button transparent>
          <nb-icon name="info" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-list>
        <nb-list-item>
          <nb-left>
            <nb-text class="text-sm">1.0_Inschrijf_form_contracten_met_Client</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text-sm">24-09-2020</nb-text>
          </nb-right>
        </nb-list-item>
        <nb-list-item>
          <nb-left>
            <nb-text class="text-sm">1.0_Inschrijf_form_contracten_met_Client</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text-sm">24-09-2020</nb-text>
          </nb-right>
        </nb-list-item>
        <nb-list-item>
          <nb-left>
            <nb-text class="text-sm">1.0_Inschrijf_form_contracten_met_Client</nb-text>
          </nb-left>
          <nb-right>
            <nb-text class="text-sm">24-09-2020</nb-text>
          </nb-right>
        </nb-list-item>
      </nb-list>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="docs"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<style>
  .text-sm {
    font-size: 12px
  }
</style>

<script>
  import FooterNav from '../included/Footer';
  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    data() {
      return {
        selectedDoc: '0'
      };
    },
    components: { FooterNav },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      }
    }
  }
</script>
