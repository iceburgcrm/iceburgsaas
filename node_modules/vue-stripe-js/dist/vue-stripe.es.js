import { defineComponent, ref, toRefs, onMounted, onBeforeUnmount, watch, openBlock, createElementBlock, computed, renderSlot, createCommentVNode } from "vue";
const ERRORS = {
  STRIPE_NOT_LOADED: "Stripe v3 library is not loaded",
  INSTANCE_NOT_DEFINED: "Instance object is not defined. Make sure you initialized Stripe before creating elements",
  ELEMENTS_NOT_DEFINED: "Elements object is not defined. You can't create stripe element without it",
  ELEMENT_TYPE_NOT_DEFINED: "elementType is required. You can't create stripe element without it"
};
const initStripe = (key, options) => {
  try {
    if (!window.Stripe) {
      throw new Error(ERRORS.STRIPE_NOT_LOADED);
    }
    const stripeInstance = window.Stripe(key, options);
    return stripeInstance;
  } catch (error) {
    console.error(error);
    return error;
  }
};
const createElements = (instance, options) => {
  try {
    if (!instance) {
      throw new Error(ERRORS.INSTANCE_NOT_DEFINED);
    }
    const elements = instance.elements(options);
    return elements;
  } catch (error) {
    console.error(error);
    return error;
  }
};
const createElement = (elements, elementType, options) => {
  try {
    if (!elements) {
      throw new Error(ERRORS.ELEMENTS_NOT_DEFINED);
    }
    if (!elementType) {
      throw new Error(ERRORS.ELEMENT_TYPE_NOT_DEFINED);
    }
    const element = elements.create(elementType, options);
    return element;
  } catch (error) {
    console.error(error);
    return error;
  }
};
var _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$1 = defineComponent({
  name: "StripeElement",
  props: {
    elements: {
      type: Object,
      required: true
    },
    type: {
      type: String,
      default: () => "card"
    },
    options: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props, { emit }) {
    const domElement = ref(document.createElement("div"));
    const stripeElement = ref();
    const mountPoint = ref();
    const { elements, type, options } = toRefs(props);
    onMounted(() => {
      const mountElement = () => {
        stripeElement.value = createElement(elements.value, type.value, options.value);
        stripeElement.value.mount(domElement.value);
        mountPoint.value.appendChild(domElement.value);
      };
      const wrapperFn = (t, e) => {
        emit(t, e);
      };
      const handleEvents = () => {
        const eventTypes = [
          "change",
          "ready",
          "focus",
          "blur",
          "click",
          "escape"
        ];
        eventTypes.forEach((t) => {
          stripeElement.value.on(t, wrapperFn.bind(null, t));
        });
      };
      try {
        mountElement();
        handleEvents();
      } catch (error) {
        console.error(error);
      }
    });
    onBeforeUnmount(() => {
      var _a, _b;
      (_a = stripeElement.value) == null ? void 0 : _a.unmount();
      (_b = stripeElement.value) == null ? void 0 : _b.destroy();
    });
    watch(options, () => {
      var _a;
      (_a = stripeElement.value) == null ? void 0 : _a.update(props.options);
    });
    return {
      stripeElement,
      domElement,
      mountPoint
    };
  }
});
const _hoisted_1$1 = { ref: "mountPoint" };
function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("div", _hoisted_1$1, null, 512);
}
var StripeElement = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1]]);
const _sfc_main = defineComponent({
  name: "StripeElements",
  props: {
    stripeKey: {
      type: String,
      required: true
    },
    instanceOptions: {
      type: Object,
      default: () => ({})
    },
    elementsOptions: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    const { stripeKey, instanceOptions, elementsOptions } = toRefs(props);
    const instance = ref();
    const elements = ref();
    const elementsUsable = computed(() => {
      return elements.value ? Object.keys(elements.value).length > 0 : false;
    });
    onMounted(() => {
      instance.value = initStripe(stripeKey.value, instanceOptions.value);
      elements.value = createElements(instance.value, elementsOptions.value);
    });
    watch(elementsOptions, () => {
      var _a;
      (_a = elements.value) == null ? void 0 : _a.update(elementsOptions.value);
    });
    return {
      elements,
      instance,
      elementsUsable
    };
  }
});
const _hoisted_1 = { key: 0 };
function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
  return _ctx.elementsUsable ? (openBlock(), createElementBlock("div", _hoisted_1, [
    renderSlot(_ctx.$slots, "default", {
      instance: _ctx.instance,
      elements: _ctx.elements
    })
  ])) : createCommentVNode("", true);
}
var StripeElements = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
export { StripeElement, StripeElements, createElement, createElements, initStripe };
