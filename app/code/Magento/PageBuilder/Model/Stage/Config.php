<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PageBuilder\Model\Stage;

class Config extends \Magento\Framework\Model\AbstractModel
{
    const DEFAULT_COMPONENT = 'Magento_PageBuilder/js/component/block/block';
    const DEFAULT_PREVIEW_COMPONENT = 'Magento_PageBuilder/js/component/block/preview/block';

    /**
     * @var \Magento\PageBuilder\Model\Config\ConfigInterface
     */
    private $configInterface;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var Config\UiComponentConfig
     */
    private $uiComponentConfig;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\PageBuilder\Model\Config\ConfigInterface $configInterface
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param Config\UiComponentConfig $uiComponentConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\PageBuilder\Model\Config\ConfigInterface $configInterface,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        Config\UiComponentConfig $uiComponentConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configInterface = $configInterface;
        $this->layoutFactory = $layoutFactory;
        $this->uiComponentConfig = $uiComponentConfig;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Return the config for the page builder instance
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'groups' => $this->getGroups(),
            'contentTypes' => $this->getContentTypes(),
            'templates' => $this->getTemplateData()
        ];
    }

    /**
     * Retrieve the content block groups
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->configInterface->getGroups();
    }

    /**
     * Build up template data
     *
     * @return array
     */
    public function getTemplateData()
    {
        return [];
    }

    /**
     * Build up the content block data
     *
     * @return array
     */
    public function getContentTypes()
    {
        $contentTypes = $this->configInterface->getContentTypes();

        $contentBlockData = [];
        foreach ($contentTypes as $name => $contentType) {
            $contentBlockData[$name] = $this->flattenContentTypeData(
                $name,
                $contentType
            );
        }

        return $contentBlockData;
    }

    /**
     * Flatten the content block data
     *
     * @param $name
     * @param $contentType
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function flattenContentTypeData($name, $contentType)
    {
        return [
            'name' => $name,
            'label' => __($contentType['label']),
            'icon' => $contentType['icon'],
            'form' => $contentType['form'],
            'contentType' => '',
            'group' => (isset($contentType['group'])
                ? $contentType['group'] : 'general'),
            'fields' => $this->uiComponentConfig->getFields($contentType['form']),
            'preview_template' => (isset($contentType['preview_template'])
                ? $contentType['preview_template'] : ''),
            'render_template' => (isset($contentType['render_template'])
                ? $contentType['render_template'] : ''),
            'preview_component' => (isset($contentType['preview_component'])
                ? $contentType['preview_component']
                : self::DEFAULT_PREVIEW_COMPONENT),
            'component' => (isset($contentType['component'])
                ? $contentType['component'] : self::DEFAULT_COMPONENT),
            'allowed_parents' => isset($contentType['allowed_parents'])
                ? explode(',', $contentType['allowed_parents']) : [],
            'readers' => isset($contentType['readers']) ? $contentType['readers'] : [],
            'appearances' => isset($contentType['appearances']) ? $contentType['appearances'] : [],
            'is_visible' => isset($contentType['is_visible']) && $contentType['is_visible'] === 'false' ? false : true
        ];
    }
}
