import './style';
import register from 'ShopUi/app/registry';
export default register('volume-price', () => import(/* webpackMode: "eager" */'./volume-price'));
