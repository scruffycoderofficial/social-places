<?php

namespace Oro\Bundle\SecurityBundle\Tests\Unit\Util;

use Doctrine\ORM\Mapping\ClassMetadata;
use Oro\Bundle\SecurityBundle\Tests\Unit\Fixtures\Models\CMS\CmsAddress;
use Oro\Bundle\SecurityBundle\Tests\Unit\Fixtures\Models\CMS\CmsArticle;
use Oro\Bundle\SecurityBundle\Tests\Unit\Fixtures\Models\CMS\CmsUser;
use Oro\Bundle\SecurityBundle\Util\PropertyPathSecurityHelper;
use Symfony\Component\Security\Acl\Voter\FieldVote;

class PropertyPathSecurityHelperTest extends \PHPUnit\Framework\TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $authorizationChecker;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $managerRegistry;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $entityConfigProvider;

    /** @var PropertyPathSecurityHelper */
    protected $helper;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this
            ->createMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->managerRegistry = $this->getMockBuilder('Doctrine\Persistence\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityConfigProvider = $this->getMockBuilder('Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = new PropertyPathSecurityHelper(
            $this->authorizationChecker,
            $this->managerRegistry,
            $this->entityConfigProvider
        );
    }

    public function testIsGrantedByPropertyPath()
    {
        $address = new CmsAddress();
        $address->city = 'test';
        $user = new CmsUser();
        $user->id = 1;
        $user->setAddress($address);
        $article = new CmsArticle();
        $article->setAuthor($user);

        $addressMetadata = new ClassMetadata(get_class($address));
        $userMetadata = new ClassMetadata(get_class($user));
        $user->associationMappings = [
            'address' => ['targetEntity' => get_class($address)]
        ];
        $articleMetadata = new ClassMetadata(get_class($article));
        $articleMetadata->associationMappings = [
            'user' => ['targetEntity' => get_class($user)]
        ];

        $propertyPath = 'user.address.city';

        $em = $this->getMockBuilder('Doctrine\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->managerRegistry->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($em);
        $em->expects($this->any())
            ->method('getClassMetadata')
            ->willReturnMap(
                [
                    [get_class($address), $addressMetadata],
                    [get_class($user), $userMetadata],
                    [get_class($article), $articleMetadata]
                ]
            );

        $this->authorizationChecker->expects($this->any())
            ->method('isGranted')
            ->willReturnCallback(function ($permission, $object) {
                if ($object instanceof FieldVote) {
                    if ($object->getField() === 'city') {
                        return false;
                    }
                }

                return true;
            });

        $isGranted = $this->helper->isGrantedByPropertyPath($article, $propertyPath, 'EDIT');
        $this->assertFalse($isGranted);
    }

    public function testIisGrantedByPropertyPathOnWrongClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Can't get entity manager for class stdClass");

        $this->helper->isGrantedByPropertyPath(new \stdClass(), 'somePath', 'EDIT');
    }
}
