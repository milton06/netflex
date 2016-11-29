<?php

namespace NetFlex\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\DeliveryChargeBundle\Entity\Currency;

/**
 * Price
 *
 * @ORM\Table(name="order_prices")
 * @ORM\Entity(repositoryClass="NetFlex\OrderBundle\Repository\PriceRepository")
 */
class Price
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
	/**
	 * @ORM\OneToOne(targetEntity="OrderTransaction", inversedBy="orderPrice")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 */
	private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="order_base_charge", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank(
     *     message="Required field"
     * )
     */
    private $orderBaseCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_extra_weight_levied_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderExtraWeightLeviedCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_user_base_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderUserBaseCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_user_extra_weight_levied_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderUserExtraWeightLeviedCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_invoice_price", type="decimal", precision=10, scale=2)
     *
     * @Assert\NotBlank(
     *     message="Required field"
     * )
     */
    private $orderInvoicePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="order_cod_payment_added_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderCodPaymentAddedCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_fuel_surcharge_added_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderFuelSurchargeAddedCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_service_tax_added_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderServiceTaxAddedCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_carrier_risk_added_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderCarrierRiskAddedCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_octroi_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderOctroiCharge;

    /**
     * @var string
     *
     * @ORM\Column(name="order_return_charge", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $orderReturnCharge;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\NetFlex\DeliveryChargeBundle\Entity\Currency")
	 * @ORM\JoinColumn(name="order_price_unit_id", referencedColumnName="id")
	 *
	 * @Assert\NotBlank(
	 *     message="Required field"
	 * )
	 */
	private $orderPriceUnitId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
	 * Set orderId
	 *
	 * @param OrderTransaction $orderId
	 *
	 * @return Price
	 */
	public function setOrderId(OrderTransaction $orderId = null)
	{
		$this->orderId = $orderId;
		
		return $this;
	}
	
	/**
	 * Get orderId
	 *
	 * @return OrderTransaction
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}

    /**
     * Set orderBaseCharge
     *
     * @param string $orderBaseCharge
     *
     * @return Price
     */
    public function setOrderBaseCharge($orderBaseCharge)
    {
        $this->orderBaseCharge = $orderBaseCharge;

        return $this;
    }

    /**
     * Get orderBaseCharge
     *
     * @return string
     */
    public function getOrderBaseCharge()
    {
        return $this->orderBaseCharge;
    }

    /**
     * Set orderExtraWeightLeviedCharge
     *
     * @param string $orderExtraWeightLeviedCharge
     *
     * @return Price
     */
    public function setOrderExtraWeightLeviedCharge($orderExtraWeightLeviedCharge)
    {
        $this->orderExtraWeightLeviedCharge = $orderExtraWeightLeviedCharge;

        return $this;
    }

    /**
     * Get orderExtraWeightLeviedCharge
     *
     * @return string
     */
    public function getOrderExtraWeightLeviedCharge()
    {
        return $this->orderExtraWeightLeviedCharge;
    }

    /**
     * Set orderUserBaseCharge
     *
     * @param string $orderUserBaseCharge
     *
     * @return Price
     */
    public function setOrderUserBaseCharge($orderUserBaseCharge)
    {
        $this->orderUserBaseCharge = $orderUserBaseCharge;

        return $this;
    }

    /**
     * Get orderUserBaseCharge
     *
     * @return string
     */
    public function getOrderUserBaseCharge()
    {
        return $this->orderUserBaseCharge;
    }

    /**
     * Set orderUserExtraWeightLeviedCharge
     *
     * @param string $orderUserExtraWeightLeviedCharge
     *
     * @return Price
     */
    public function setOrderUserExtraWeightLeviedCharge($orderUserExtraWeightLeviedCharge)
    {
        $this->orderUserExtraWeightLeviedCharge = $orderUserExtraWeightLeviedCharge;

        return $this;
    }

    /**
     * Get orderUserExtraWeightLeviedCharge
     *
     * @return string
     */
    public function getOrderUserExtraWeightLeviedCharge()
    {
        return $this->orderUserExtraWeightLeviedCharge;
    }

    /**
     * Set orderInvoicePrice
     *
     * @param string $orderInvoicePrice
     *
     * @return Price
     */
    public function setOrderInvoicePrice($orderInvoicePrice)
    {
        $this->orderInvoicePrice = $orderInvoicePrice;

        return $this;
    }

    /**
     * Get orderInvoicePrice
     *
     * @return string
     */
    public function getOrderInvoicePrice()
    {
        return $this->orderInvoicePrice;
    }

    /**
     * Set orderCodPaymentAddedCharge
     *
     * @param string $orderCodPaymentAddedCharge
     *
     * @return Price
     */
    public function setOrderCodPaymentAddedCharge($orderCodPaymentAddedCharge)
    {
        $this->orderCodPaymentAddedCharge = $orderCodPaymentAddedCharge;

        return $this;
    }

    /**
     * Get orderCodPaymentAddedCharge
     *
     * @return string
     */
    public function getOrderCodPaymentAddedCharge()
    {
        return $this->orderCodPaymentAddedCharge;
    }

    /**
     * Set orderFuelSurchargeAddedCharge
     *
     * @param string $orderFuelSurchargeAddedCharge
     *
     * @return Price
     */
    public function setOrderFuelSurchargeAddedCharge($orderFuelSurchargeAddedCharge)
    {
        $this->orderFuelSurchargeAddedCharge = $orderFuelSurchargeAddedCharge;

        return $this;
    }

    /**
     * Get orderFuelSurchargeAddedCharge
     *
     * @return string
     */
    public function getOrderFuelSurchargeAddedCharge()
    {
        return $this->orderFuelSurchargeAddedCharge;
    }

    /**
     * Set orderServiceTaxAddedCharge
     *
     * @param string $orderServiceTaxAddedCharge
     *
     * @return Price
     */
    public function setOrderServiceTaxAddedCharge($orderServiceTaxAddedCharge)
    {
        $this->orderServiceTaxAddedCharge = $orderServiceTaxAddedCharge;

        return $this;
    }

    /**
     * Get orderServiceTaxAddedCharge
     *
     * @return string
     */
    public function getOrderServiceTaxAddedCharge()
    {
        return $this->orderServiceTaxAddedCharge;
    }

    /**
     * Set orderCarrierRiskAddedCharge
     *
     * @param string $orderCarrierRiskAddedCharge
     *
     * @return Price
     */
    public function setOrderCarrierRiskAddedCharge($orderCarrierRiskAddedCharge)
    {
        $this->orderCarrierRiskAddedCharge = $orderCarrierRiskAddedCharge;

        return $this;
    }

    /**
     * Get orderCarrierRiskAddedCharge
     *
     * @return string
     */
    public function getOrderCarrierRiskAddedCharge()
    {
        return $this->orderCarrierRiskAddedCharge;
    }

    /**
     * Set orderOctroiCharge
     *
     * @param string $orderOctroiCharge
     *
     * @return Price
     */
    public function setOrderOctroiCharge($orderOctroiCharge)
    {
        $this->orderOctroiCharge = $orderOctroiCharge;

        return $this;
    }

    /**
     * Get orderOctroiCharge
     *
     * @return string
     */
    public function getOrderOctroiCharge()
    {
        return $this->orderOctroiCharge;
    }

    /**
     * Set orderReturnCharge
     *
     * @param string $orderReturnCharge
     *
     * @return Price
     */
    public function setOrderReturnCharge($orderReturnCharge)
    {
        $this->orderReturnCharge = $orderReturnCharge;

        return $this;
    }

    /**
     * Get orderReturnCharge
     *
     * @return string
     */
    public function getOrderReturnCharge()
    {
        return $this->orderReturnCharge;
    }
	
	/**
	 * Set orderPriceUnitId
	 *
	 * @param Currency $orderPriceUnitId
	 *
	 * @return Item
	 */
	public function setOrderPriceUnitId(Currency $orderPriceUnitId = null)
	{
		$this->orderPriceUnitId = $orderPriceUnitId;
		
		return $this;
	}
	
	/**
	 * Get orderPriceUnitId
	 *
	 * @return WeightUnit
	 */
	public function getOrderPriceUnitId()
	{
		return $this->orderPriceUnitId;
	}
}
