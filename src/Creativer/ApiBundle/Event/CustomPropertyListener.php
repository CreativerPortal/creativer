<?php
//namespace Creativer\ApiBundle\Event;
//
//use Elastica\Document;
//use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
//
//
//class CustomPropertyListener implements \FOS\ElasticaBundle\Transformer\ModelToElasticaTransformerInterface
//{
//    public function transform($article, array $fields)
//    {
//
//        $identifier = $article->getId();
//
//
//        $values = array(
//            'id' => $article->getId(),
//            'category' => "asas"
//        );
//
//        //Create a document to index
//        $document = new Document($identifier,$values);
//        $document->setParent(1);
//
//        return $document;
//    }
//}