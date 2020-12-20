<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Documenten Schuldeiser</nb-title>
      </nb-body>
      <nb-right>
        <nb-button transparent>
          <nb-icon name="info" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="zoek schuldeiser documenten" />
      </nb-item>
      <nb-list>
        <nb-list-item>
          <nb-left>
            <nb-text>aanmaning ziggo</nb-text>
          </nb-left>
          <nb-body>
            <nb-text>24-09-2020</nb-text>
          </nb-body>
        </nb-list-item>
        <nb-list-item>
          <nb-left>
            <nb-text>aanmaning ziggo</nb-text>
          </nb-left>
          <nb-body>
            <nb-text>24-09-2020</nb-text>
          </nb-body>
        </nb-list-item>
        <nb-list-item>
          <nb-left>
            <nb-text>aanmaning ziggo</nb-text>
          </nb-left>
          <nb-body>
            <nb-text>24-09-2020</nb-text>
          </nb-body>
        </nb-list-item>
      </nb-list>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="docs"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../included/Footer';
  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav },
    data() {
      return {
        selectedDoc: '0'
      };
    },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      }
    }
  }
</script>
